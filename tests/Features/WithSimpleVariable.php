<?php

it('works', function () {
    expect('<x-simple-variable color="indigo" />')->toHaveExtractedClasses(['bg-indigo-500']);
});
