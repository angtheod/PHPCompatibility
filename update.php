<?php
/**
 * This repo is a clone of the wimg/PHPCompatibility repository.
 * Since this repository is not quite composer friendly, this repository tries fixing this, using a git subtree.
 *
 * This script simply updates the subtree and mimics the version tags on the upstream repository.
 */

// Fetch remote tags
$remoteTags = [];
exec(
    "git ls-remote --tags --refs https://github.com/wimg/PHPCompatibility.git | awk '{print $2}' | cut -d '/' -f 3",
    $remoteTags
);

// Find local tags
$localTags = [];
exec("git tag -l", $localTags);

// Add remote repo if it does not exists
if (file_exists("./PHPCompatibility")===false) {
  $firstTag = array_shift($remoteTags);
  passthru("git subtree add --prefix PHPCompatibility https://github.com/wimg/PHPCompatibility.git $firstTag");
  passthru("git tag -a $firstTag -m '$firstTag'");
}

// Find the missing ones
$missingTags = array_diff($remoteTags, $localTags);

// Replicate each tag
foreach ($missingTags as $missingTag) {
  passthru("git subtree pull --prefix PHPCompatibility https://github.com/wimg/PHPCompatibility.git $missingTag");
  passthru("git tag -a $missingTag -m '$missingTag'");
}

// Update master
passthru("git subtree pull --prefix PHPCompatibility https://github.com/wimg/PHPCompatibility.git master");

// Push
passthru("git push");

// Push tags
passthru("git push --tags");
