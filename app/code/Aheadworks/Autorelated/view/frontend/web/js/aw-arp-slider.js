/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget for slider block
 *
 * @method init()
 * @method recalculateWidth()
 */
define([
    'jquery',
    'Aheadworks_Autorelated/js/aw-arp-abstract',
    'slick',
    'jquery/ui'
], function($) {
    "use strict";

    $.widget('mage.awArpSlider', $.mage.awArpAbstract, {
        /** @inheritdoc */
        init: function()
        {
            this._super();
            this.initSlickPlugin();
        },

        /**
         * Initialize Slick plugin
         */
        initSlickPlugin: function () {
            this.element.find(this.options.itemsSelector).slick({
                adaptiveHeight: false,
                autoplay: false,
                autoplaySpeed: 3000,
                arrows: true,
                dots: false,
                pauseOnHover: true,
                pauseOnDotsHover: false,
                respondTo: 'slider',
                responsive: [
                    {
                        breakpoint: 1000,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 4
                        }
                    },
                    {
                        breakpoint: 800,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3
                        }
                    },
                    {
                        breakpoint: 500,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            adaptiveHeight: true
                        }
                    }
                ],
                slidesToShow: 5,
                slidesToScroll: 5
            });
            this.element.css('opacity', '1');
        },

        /**
         * Recalculation of the block width depending on the width of the screen
         */
        recalculateParams: function() {
            var mainContent = this.element.closest('#maincontent');

            if (mainContent.length) {
                if (mainContent.width() < 768) {
                    var column = this.element.closest('.columns > .column.main, .columns > .sidebar'),
                        sliderWidth = column.length && column.width() < mainContent.width()
                        ? column.width()
                        : mainContent.width();
                    this.element.outerWidth(sliderWidth);
                    this.element.find(this.options.itemsSelector).width(this.element.width());
                } else {
                    this.element.css('width', '');
                    this.element.find(this.options.itemsSelector).css('width', '');
                }
            }
        }
    });

    return $.mage.awArpSlider;
});
