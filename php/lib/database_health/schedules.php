<?php

// Turn on event scheduler
$db->conn->query("SET GLOBAL event_scheduler = ON;");

// Event name
$events = [
    /* EXAMPLE:
    [
        "schedule_event_name",
        "schedule query goes here"
    ],*/
    [
        "user_data_refresh_for_user_$userID",
        "CREATE EVENT <event_name>
        ON SCHEDULE EVERY 12 HOUR
        DO
        BEGIN
            UPDATE users SET posts=(SELECT COUNT(*) FROM posts WHERE userID=$userID) WHERE userID=$userID;
        END;"
    ],
];

// Iterate over events
foreach ($events as $event) {
    // Fetch event name
    $eventName = $event[0];
    // Fetch event query
    $eventQuery = str_replace("<event_name>", $eventName, $event[1]);

    // Check if event already exists
    $res = $db->select("SELECT COUNT(*) AS event_count FROM information_schema.events WHERE event_name = ?;", "s", $eventName);
    // Event count
    $event_count = mysqli_fetch_assoc($res)["event_count"];
    if ($event_count === 0) {
        // The event doesn't exist, create it
        $db->createEvent($eventQuery);
    }
}