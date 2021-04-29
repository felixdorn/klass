<?php

it('works', function () {
    expect('<x-variable-in-string width="full" />')->toHaveExtractedClasses(['w-full']);
});
