<?php

it('works', function () {
    expect('<x-variable-defined-in-the-constructor-method />')->toHaveExtractedClasses(['font-2xl']);
});
