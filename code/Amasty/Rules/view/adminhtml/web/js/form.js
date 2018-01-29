define([
    'jquery',
    'uiRegistry',
    'mage/translate',
], function ($, registry) {
    var amrulesForm = {
        update: function (type) {
            var action = '';
            this.resetFields(type);

            var actionFieldset = $('#' + type +'rule_actions_fieldset_').parent();

            window.amRulesHide = 0;

            actionFieldset.show();
            if (typeof window.amPromoHide !="undefined" && window.amPromoHide == 1) {
                actionFieldset.hide();
            }

            var selector = $('[data-index="simple_action"] select');
            if (type !== 'sales_rule_form') {
                action = selector[1] ? selector[1].value : selector[0].value;
            } else {
                action = selector.val();
            }

            this.renameRulesSetting(action);

            switch (action) {
                case 'thecheapest':
                case 'themostexpencive':
                case 'moneyamount':
                case 'aftern_fixed':
                case 'aftern_disc':
                case 'aftern_fixdisc':
                case 'eachn_perc':
                case 'eachn_fixdisc':
                case 'eachn_fixprice':
                case 'groupn':
                case 'groupn_disc':
                    this.showFields(['amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]'], type);
                    break;
                case 'eachmaftn_perc':
                case 'eachmaftn_fixdisc':
                case 'eachmaftn_fixprice':
                    this.showFields(['amrulesrule[eachm]', 'amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]'], type);
                    break;
                case 'buyxgetn_perc':
                case 'buyxgetn_fixprice':
                case 'buyxgetn_fixdisc':
                    this.showFields(['amrulesrule[nqty]', 'amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]'], type);
                    this.showPromoItems();
                    this.showNote();
                    break;
                case 'setof_percent':
                case 'setof_fixed':
                    actionFieldset.hide();
                    window.amRulesHide = 1;
                    this.showFields(['amrulesrule[max_discount]'], type);
                    this.showPromoItems();

                    //this.hideFields(['discount_step']);
                    break;
            }
        },

        showPromoItems: function () {
            $('[data-index="promo_items"]').show();
        },

        hidePromoItems: function () {
            $('[data-index="promo_items"], [data-index="discount_step"] .admin__field-note').hide();
        },

        showNote: function () {
            $('[data-index="discount_step"] .admin__field-note').show();
        },

        resetFields: function (type) {
            this.showFields([
                'discount_qty', 'discount_step', 'apply_to_shipping', 'simple_free_shipping'
            ], type);
            this.hideFields([
                'amrulesrule[skip_rule]',
                'amrulesrule[max_discount]',
                'amrulesrule[nqty]',
                'amrulesrule[priceselector]',
                'amrulesrule[eachm]'
            ], type);
            this.hidePromoItems();
        },

        hideFields: function (names, type) {
            return this.toggleFields('hide', names, type);
        },

        showFields: function (names, type) {
            return this.toggleFields('show', names, type);
        },

        addPrefix: function (names, type) {
            for (var i = 0; i < names.length; i++) {
                names[i] = type + '.' + type + '.' + 'actions.' + names[i];
            }

            return names;
        },

        toggleFields: function (method, names, type) {
            registry.get(this.addPrefix(names, type), function () {
                for (var i = 0; i < arguments.length; i++) {
                    arguments[i][method]();
                }
            });
        },

        renameRulesSetting: function (action) {
            var discountStep = $('[data-index="discount_step"] label span'),
                discountAmount = $('[data-index="discount_amount"] label span');
            switch (action) {
                case 'buy_x_get_y':
                    discountStep.text($.mage.__("Buy N Products"));
                    discountAmount.text($.mage.__("Number of Products with Discount"));
                    break;
                case 'eachn_perc':
                case 'eachn_fixdisc':
                case 'eachn_fixprice':
                    discountStep.text($.mage.__("Each N-th"));
                    break;
                case 'eachmaftn_perc':
                case 'eachmaftn_fixdisc':
                case 'eachmaftn_fixprice':
                    discountStep.text($.mage.__("Each Product (step)"));
                    break;
                case 'buyxgetn_perc':
                case 'buyxgetn_fixprice':
                case 'buyxgetn_fixdisc':
                    discountStep.text($.mage.__("Number of X Products"));
                    break;
                default:
                    discountStep.text($.mage.__("Discount Qty Step (Buy X)"));
                    discountAmount.text($.mage.__("Discount Amount"));
                    break;
            }
        }
    };

    return amrulesForm;
});