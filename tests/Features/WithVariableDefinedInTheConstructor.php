<?php

it('works', function () {
    expect('<x-variable-defined-in-the-constructor />')->toHaveExtractedClasses(['font-lg']);
});
