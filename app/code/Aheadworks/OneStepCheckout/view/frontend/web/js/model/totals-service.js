/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    ['ko'],
    function (ko) {
        'use strict';

        var isLoading = ko.observable(false);

        return {
            isLoading: isLoading
        };
    }
);
