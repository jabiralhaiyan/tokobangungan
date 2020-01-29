function generateCardNo(x) {
    if(!x) { x = 16; }
    var achars = "1234567890", no = "";
    for (var i=0; i<x; i++) {
        var rnum = Math.floor(Math.random() * achars.length);
        no += achars.substring(rnum,rnum+1);
    }
    return no;
}
function roundNumber(number, toref) {
    switch(toref) {
        case 1:
            var rn = formatDecimal(Math.round(number * 20)/20);
            break;
        case 2:
            var rn = formatDecimal(Math.round(number * 2)/2);
            break;
        case 3:
            var rn = formatDecimal(Math.round(number));
            break;
        case 4:
            var rn = formatDecimal(Math.ceil(number));
            break;
        default:
            var rn = number;
    }
    return rn;
}
function getNumber(x) {
    return accounting.unformat(x);
}
function formatQuantity(x) {
    return (x != null) ? '<div class="text-center">'+formatNumber(x, Settings.qty_decimals)+'</div>' : '';
}
function formatQuantity2(x) {
    return (x != null) ? formatNumber(x, Settings.qty_decimals) : '';
}
function formatNumber(x, d) {
    if(!d && d != 0) { d = Settings.decimals; }
    if(Settings.sac == 1) {
        return formatSA(parseFloat(x).toFixed(d));
    }
    return accounting.formatNumber(x, d, Settings.thousands_sep == 0 ? ' ' : Settings.thousands_sep, Settings.decimals_sep);
}
function formatMoney(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(Settings.sac == 1) {
        return (Settings.display_symbol == 1 ? Settings.symbol : '') +
            ''+formatSA(parseFloat(x).toFixed(Settings.decimals)) +
            (Settings.display_symbol == 2 ? Settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, Settings.decimals, Settings.thousands_sep == 0 ? ' ' : Settings.thousands_sep, Settings.decimals_sep, "%s%v");
    return (Settings.display_symbol == 1 ? Settings.symbol : '') +
        fmoney +
        (Settings.display_symbol == 2 ? Settings.symbol : '');
}
function formatDecimal(x, d) {
    if (!d) { d = Settings.decimals; }
    return parseFloat(accounting.formatNumber(x, d, '', '.'));
}
function is_valid_discount(mixed_var) {
    return (is_numeric(mixed_var) || (/([0-9]%)/i.test(mixed_var))) ? true : false;
}
function is_numeric(mixed_var) {
    var whitespace =
        " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
            1)) && mixed_var !== '' && !isNaN(mixed_var);
}
function is_float(mixed_var) {
    return +mixed_var === mixed_var && (!isFinite(mixed_var) || !! (mixed_var % 1));
}
function currencyFormat(x) {
    if (x != null) {
        return '<div class="text-right">'+formatMoney(x)+'</div>';
    } else {
        return '<div class="text-right">0</div>';
    }
}
function quantityFormat(x) {
    if (x != null) {
        return '<div class="text-center">'+formatQuantity(x)+'</div>';
    } else {
        return '<div class="text-center">0</div>';
    }
}
function formatSA (x) {
    x=x.toString();
    var afterPoint = '';
    if(x.indexOf('.') > 0)
       afterPoint = x.substring(x.indexOf('.'),x.length);
    x = Math.floor(x);
    x=x.toString();
    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;
    return res;
}

function cf(x) { return currencyFormat(x); }
function pf(x) { return parseFloat(x); }

function read_card() {
    $('.swipe').keypress( function (e) {

        var payid = $(this).attr('id'),
            id = payid.substr(payid.length - 1);
        var TrackData = $(this).val();
        if (e.keyCode == 13) {
            e.preventDefault();

            var p = new SwipeParserObj(TrackData);

            if(p.hasTrack1)
            {
                // Populate form fields using track 1 data
                var CardType = null;
                var ccn1 = p.account.charAt(0);
                if(ccn1 == 4)
                    CardType = 'Visa';
                else if(ccn1 == 5)
                    CardType = 'MasterCard';
                else if(ccn1 == 3)
                    CardType = 'Amex';
                else if(ccn1 == 6)
                    CardType = 'Discover';
                else
                    CardType = 'Visa';

                $('#pcc_no_'+id).val(p.account);
                $('#pcc_holder_'+id).val(p.account_name);
                $('#pcc_month_'+id).val(p.exp_month);
                $('#pcc_year_'+id).val(p.exp_year);
                $('#pcc_cvv2_'+id).val('');
                $('#pcc_type_'+id).val(CardType);

            }
            else
            {
                $('#pcc_no_'+id).val('');
                $('#pcc_holder_'+id).val('');
                $('#pcc_month_'+id).val('');
                $('#pcc_year_'+id).val('');
                $('#pcc_cvv2_'+id).val('');
                $('#pcc_type_'+id).val('');
            }

            $('#pcc_cvv2_'+id).focus();
        }

    }).blur(function (e) {
        $(this).val('');
    }).focus( function (e) {
        $(this).val('');
    });
}

function get(name) {
    if (typeof (Storage) !== "undefined") {
        return localStorage.getItem(name);
    } else {
        alert('Please use a modern browser as this site needs localstroage!');
    }
}

function store(name, val) {
    if (typeof (Storage) !== "undefined") {
        localStorage.setItem(name, val);
    } else {
        alert('Please use a modern browser as this site needs localstroage!');
    }
}

function remove(name) {
    if (typeof (Storage) !== "undefined") {
        localStorage.removeItem(name);
    } else {
        alert('Please use a modern browser as this site needs localstroage!');
    }
}

function hrsd(sdate) {
    if (sdate !== null) {
        return date(dateformat, strtotime(sdate));
    }
    return sdate;
}

function hrld(ldate) {
    if (ldate !== null) {
        return date(dateformat+' '+timeformat, strtotime(ldate));
    }
    return ldate;
}

$(document).ajaxStart(function(){
    $('#ajaxCall').show();
}).ajaxStop(function(){
    $('#ajaxCall').hide();
});

$(document).ready(function() {
    // $('[data-toggle="ajax-modal"]').click(function (e) {
    $(document).on('click', '[data-toggle="ajax-modal"]', function (e) {
        e.preventDefault();
        var link = $(this).attr('href');
        $.get(link).done(function(data) {
            $('#myModal').html(data)
            // .append("<script src='"+assets+"js/modal.js' />")
            .modal({backdrop: 'static'});
        });
        return false;
    });
    $('.load_suspended').click(function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        if(get('spositems')) {
            bootbox.confirm(lang.r_u_sure, function(result) {
                if(result == true) {
                    window.location.href = href;
                }
            });
            return false;
        } else {
            window.location.href = href;
        }
    });
    $('.sign_out').click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        bootbox.confirm('<strong>'+lang.register_open_alert+'</strong>', function(result) {
            if(result == true) {
                window.location.href = href;
            }
        });
        return false;
    });
});
