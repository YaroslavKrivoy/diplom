/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'ko'
    ],
    function(ko) {
        'use strict';

        var isShownFlag = ko.observable(false);

        return {
            isShown: isShownFlag
        };
    }
);
