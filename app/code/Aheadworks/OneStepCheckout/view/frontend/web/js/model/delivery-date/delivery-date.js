/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    ['ko'],
    function (ko) {
        'use strict';

        var date = ko.observable(''),
            timeSlot = ko.observable('');

        return {
            date: date,
            timeSlot: timeSlot
        };
    }
);
