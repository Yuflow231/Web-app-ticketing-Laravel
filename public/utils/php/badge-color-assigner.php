<?php
function setBadgeColor(string $tag): void {
    $tagLower = strtolower($tag);
    $color = "blue"; // Default

    if ($tagLower === "in progress" || $tagLower === "completed" || $tagLower === "low" || $tagLower === "included") {
        $color = "green";
    } elseif ($tagLower === "on hold" || $tagLower === "medium") {
        $color = "orange";
    } elseif ($tagLower === "closed" || $tagLower === "high" || $tagLower === "billed") {
        $color = "red";
    }

    echo $color;
}
