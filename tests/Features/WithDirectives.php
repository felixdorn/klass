<?php

it('works', function () {
    expect('<x-with-directives />')->toHaveExtractedClasses(['bg-blue-500']);
});
