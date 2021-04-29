<?php

it('works', function () {
    expect('<x-variable-defined-in-public-property />')->toHaveExtractedClasses(['rounded-full']);
});
