<?php

it('works', function () {
    expect('<x-background />')->toHaveExtractedClasses(['bg-indigo-500']);
});
