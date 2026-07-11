package com.cadence.engine;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Timestamp;
import java.time.LocalDate;
import java.time.LocalDateTime;

/**
 * Batched insert helper for the seed path. Participant inserts run
 * immediately (their generated id is needed for the check-ins that
 * follow); everything else accumulates and flushes in large batches,
 * which with rewriteBatchedStatements=true collapses to multi-row
 * INSERTs and keeps the full demo seed under the two-minute budget.
 */
final class Batcher {

    private static final int FLUSH_AT = 1000;

    private final PreparedStatement participant;
    private final PreparedStatement participantUpdate;
    private final PreparedStatement checkin;
    private final PreparedStatement event;
    private final PreparedStatement notification;
    private final PreparedStatement badge;

    private int checkins;
    private int events;
    private int notifications;
    private int badges;
    private int participantUpdates;

    Batcher(Connection conn) throws SQLException {
        participant = conn.prepareStatement(
            "INSERT INTO challenge_participants (challenge_id, user_id, joined_at) VALUES (?, ?, ?)",
            Statement.RETURN_GENERATED_KEYS);
        participantUpdate = conn.prepareStatement(
            "UPDATE challenge_participants SET current_streak = ?, longest_streak = ?, points = ?, last_checkin_date = ? WHERE id = ?");
        checkin = conn.prepareStatement(
            "INSERT INTO check_ins (participant_id, user_id, challenge_id, checkin_date, note, points_awarded, created_at) "
            + "VALUES (?, ?, ?, ?, ?, ?, ?)");
        event = conn.prepareStatement(
            "INSERT INTO activity_events (user_id, type, challenge_id, badge_id, meta, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        notification = conn.prepareStatement(
            "INSERT INTO notifications (user_id, type, title, body, link, read_at, created_at) VALUES (?, ?, ?, ?, NULL, ?, ?)");
        badge = conn.prepareStatement(
            "INSERT IGNORE INTO user_badges (user_id, badge_id, challenge_id, earned_at) VALUES (?, ?, ?, ?)");
    }

    long insertParticipant(long challengeId, long userId, LocalDateTime joinedAt) throws SQLException {
        participant.setLong(1, challengeId);
        participant.setLong(2, userId);
        participant.setTimestamp(3, Timestamp.valueOf(joinedAt));
        participant.executeUpdate();
        try (ResultSet keys = participant.getGeneratedKeys()) {
            keys.next();
            return keys.getLong(1);
        }
    }

    void updateParticipant(long id, int streak, int longest, int points, LocalDate lastDate) throws SQLException {
        participantUpdate.setInt(1, streak);
        participantUpdate.setInt(2, longest);
        participantUpdate.setInt(3, points);
        if (lastDate != null) {
            participantUpdate.setString(4, lastDate.toString());
        } else {
            participantUpdate.setNull(4, java.sql.Types.DATE);
        }
        participantUpdate.setLong(5, id);
        participantUpdate.addBatch();
        if (++participantUpdates >= FLUSH_AT) {
            participantUpdate.executeBatch();
            participantUpdates = 0;
        }
    }

    void insertCheckin(long participantId, long userId, long challengeId, LocalDate date,
                       int points, LocalDateTime createdAtUtc, String note) throws SQLException {
        checkin.setLong(1, participantId);
        checkin.setLong(2, userId);
        checkin.setLong(3, challengeId);
        checkin.setString(4, date.toString());
        if (note != null) {
            checkin.setString(5, note);
        } else {
            checkin.setNull(5, java.sql.Types.VARCHAR);
        }
        checkin.setInt(6, points);
        checkin.setTimestamp(7, Timestamp.valueOf(createdAtUtc));
        checkin.addBatch();
        if (++checkins >= FLUSH_AT) {
            checkin.executeBatch();
            checkins = 0;
        }
    }

    void insertEvent(long userId, String type, Long challengeId, Long badgeId, String metaJson,
                     LocalDateTime createdAtUtc) throws SQLException {
        event.setLong(1, userId);
        event.setString(2, type);
        if (challengeId != null) {
            event.setLong(3, challengeId);
        } else {
            event.setNull(3, java.sql.Types.BIGINT);
        }
        if (badgeId != null) {
            event.setLong(4, badgeId);
        } else {
            event.setNull(4, java.sql.Types.BIGINT);
        }
        if (metaJson != null) {
            event.setString(5, metaJson);
        } else {
            event.setNull(5, java.sql.Types.VARCHAR);
        }
        event.setTimestamp(6, Timestamp.valueOf(createdAtUtc));
        event.addBatch();
        if (++events >= FLUSH_AT) {
            event.executeBatch();
            events = 0;
        }
    }

    void insertNotification(long userId, String type, String title, String body,
                            LocalDateTime createdAtUtc, LocalDateTime readAtUtc) throws SQLException {
        notification.setLong(1, userId);
        notification.setString(2, type);
        notification.setString(3, title);
        notification.setString(4, body);
        if (readAtUtc != null) {
            notification.setTimestamp(5, Timestamp.valueOf(readAtUtc));
        } else {
            notification.setNull(5, java.sql.Types.TIMESTAMP);
        }
        notification.setTimestamp(6, Timestamp.valueOf(createdAtUtc));
        notification.addBatch();
        if (++notifications >= FLUSH_AT) {
            notification.executeBatch();
            notifications = 0;
        }
    }

    void insertBadge(long userId, Long badgeId, Long challengeId, LocalDateTime earnedAtUtc) throws SQLException {
        if (badgeId == null) {
            return;
        }
        badge.setLong(1, userId);
        badge.setLong(2, badgeId);
        if (challengeId != null) {
            badge.setLong(3, challengeId);
        } else {
            badge.setNull(3, java.sql.Types.BIGINT);
        }
        badge.setTimestamp(4, Timestamp.valueOf(earnedAtUtc));
        badge.addBatch();
        if (++badges >= FLUSH_AT) {
            badge.executeBatch();
            badges = 0;
        }
    }

    void flush() throws SQLException {
        checkin.executeBatch();
        event.executeBatch();
        notification.executeBatch();
        badge.executeBatch();
        participantUpdate.executeBatch();
        checkins = events = notifications = badges = participantUpdates = 0;
    }
}
