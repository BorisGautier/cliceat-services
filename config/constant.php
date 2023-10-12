<?php


//app mood
const DEMO = 'demo';
const DEV = 'dev';
const LIVE = 'live';

//
const EXAMPLE_MAIL = 'example@example.com';

//status
const ACTIVE = 'active';
const INACTIVE = 'inactive';


//management section

const MANAGEMENT_SECTION = [
    'dashboard_management' => 'dashboard_management',
    'pos_management' => 'pos_management',
    'order_management' => 'order_management',
    'product_management' => 'product_management',
    'promotion_management' => 'promotion_management',
    'help_and_support_management' => 'help_and_support_management',
    'report_and_analytics_management' => 'report_and_analytics_management',
    'user_management' => 'user_management',
    'table_management' => 'table_management',
    'system_management' => 'system_management',
];

//order status

const PENDING = 'pending';
const CONFIRMED = 'confirmed';
const PROCESSING = 'processing';
const OUT_FOR_DELIVERY = 'out_for_delivery';
const DELIVERED = 'delivered';
const RETURNED = 'returned';
const FAILED = 'failed';
const CANCELED = 'canceled';
const COOKING = 'cooking';
const COMPLETED = 'completed';

const ADD_WALLET_MESSAGE = 'add_wallet_message';
const ADD_WALLET_BONUS_MESSAGE = 'add_wallet_bonus_message';

const PAYMENT_GATEWAY_IMAGE_ARRAY = [
    'ssl_commerz_payment' => 'ssl.png',
    'razor_pay' => 'razor_pay.png',
    'paypal' => 'paypal.png',
    'stripe' => 'stripe.png',
    'paystack' => 'paystack.png',
    'bkash' => 'bkash.png',
    'paymob' => 'paymob.png',
    'flutterwave' => 'flutterwave.png',
    'mercadopago' => 'mercadopago.png',
];



/*
const COUNTRY =[
    {
        "name": "Afghanistan",
        "dialCode": "+93",
        "isoCode": "AF",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/af.svg"
    },
    {
        "name": "Aland Islands",
        "dialCode": "+358",
        "isoCode": "AX",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ax.svg"
    },
    {
        "name": "Albania",
        "dialCode": "+355",
        "isoCode": "AL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/al.svg"
    },
    {
        "name": "Algeria",
        "dialCode": "+213",
        "isoCode": "DZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/dz.svg"
    },
    {
        "name": "American Samoa",
        "dialCode": "+1684",
        "isoCode": "AS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/as.svg"
    },
    {
        "name": "Andorra",
        "dialCode": "+376",
        "isoCode": "AD",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ad.svg"
    },
    {
        "name": "Angola",
        "dialCode": "+244",
        "isoCode": "AO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ao.svg"
    },
    {
        "name": "Anguilla",
        "dialCode": "+1264",
        "isoCode": "AI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ai.svg"
    },
    {
        "name": "Antarctica",
        "dialCode": "+672",
        "isoCode": "AQ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/aq.svg"
    },
    {
        "name": "Antigua and Barbuda",
        "dialCode": "+1268",
        "isoCode": "AG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ag.svg"
    },
    {
        "name": "Argentina",
        "dialCode": "+54",
        "isoCode": "AR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ar.svg"
    },
    {
        "name": "Armenia",
        "dialCode": "+374",
        "isoCode": "AM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/am.svg"
    },
    {
        "name": "Aruba",
        "dialCode": "+297",
        "isoCode": "AW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/aw.svg"
    },
    {
        "name": "Ascension Island",
        "dialCode": "+247",
        "isoCode": "AC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ac.svg"
    },
    {
        "name": "Australia",
        "dialCode": "+61",
        "isoCode": "AU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/au.svg"
    },
    {
        "name": "Austria",
        "dialCode": "+43",
        "isoCode": "AT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/at.svg"
    },
    {
        "name": "Azerbaijan",
        "dialCode": "+994",
        "isoCode": "AZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/az.svg"
    },
    {
        "name": "Bahamas",
        "dialCode": "+1242",
        "isoCode": "BS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bs.svg"
    },
    {
        "name": "Bahrain",
        "dialCode": "+973",
        "isoCode": "BH",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bh.svg"
    },
    {
        "name": "Bangladesh",
        "dialCode": "+880",
        "isoCode": "BD",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bd.svg"
    },
    {
        "name": "Barbados",
        "dialCode": "+1246",
        "isoCode": "BB",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bb.svg"
    },
    {
        "name": "Belarus",
        "dialCode": "+375",
        "isoCode": "BY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/by.svg"
    },
    {
        "name": "Belgium",
        "dialCode": "+32",
        "isoCode": "BE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/be.svg"
    },
    {
        "name": "Belize",
        "dialCode": "+501",
        "isoCode": "BZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bz.svg"
    },
    {
        "name": "Benin",
        "dialCode": "+229",
        "isoCode": "BJ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bj.svg"
    },
    {
        "name": "Bermuda",
        "dialCode": "+1441",
        "isoCode": "BM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bm.svg"
    },
    {
        "name": "Bhutan",
        "dialCode": "+975",
        "isoCode": "BT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bt.svg"
    },
    {
        "name": "Bolivia",
        "dialCode": "+591",
        "isoCode": "BO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bo.svg"
    },
    {
        "name": "Bosnia and Herzegovina",
        "dialCode": "+387",
        "isoCode": "BA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ba.svg"
    },
    {
        "name": "Botswana",
        "dialCode": "+267",
        "isoCode": "BW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bw.svg"
    },
    {
        "name": "Brazil",
        "dialCode": "+55",
        "isoCode": "BR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/br.svg"
    },
    {
        "name": "British Indian Ocean Territory",
        "dialCode": "+246",
        "isoCode": "IO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/io.svg"
    },
    {
        "name": "Brunei Darussalam",
        "dialCode": "+673",
        "isoCode": "BN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bn.svg"
    },
    {
        "name": "Bulgaria",
        "dialCode": "+359",
        "isoCode": "BG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bg.svg"
    },
    {
        "name": "Burkina Faso",
        "dialCode": "+226",
        "isoCode": "BF",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bf.svg"
    },
    {
        "name": "Burundi",
        "dialCode": "+257",
        "isoCode": "BI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bi.svg"
    },
    {
        "name": "Cambodia",
        "dialCode": "+855",
        "isoCode": "KH",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/kh.svg"
    },
    {
        "name": "Cameroon",
        "dialCode": "+237",
        "isoCode": "CM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cm.svg"
    },
    {
        "name": "Canada",
        "dialCode": "+1",
        "isoCode": "CA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ca.svg"
    },
    {
        "name": "Cape Verde",
        "dialCode": "+238",
        "isoCode": "CV",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cv.svg"
    },
    {
        "name": "Cayman Islands",
        "dialCode": "+1345",
        "isoCode": "KY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ky.svg"
    },
    {
        "name": "Central African Republic",
        "dialCode": "+236",
        "isoCode": "CF",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cf.svg"
    },
    {
        "name": "Chad",
        "dialCode": "+235",
        "isoCode": "TD",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/td.svg"
    },
    {
        "name": "Chile",
        "dialCode": "+56",
        "isoCode": "CL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cl.svg"
    },
    {
        "name": "China",
        "dialCode": "+86",
        "isoCode": "CN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cn.svg"
    },
    {
        "name": "Christmas Island",
        "dialCode": "+61",
        "isoCode": "CX",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cx.svg"
    },
    {
        "name": "Cocos (Keeling) Islands",
        "dialCode": "+61",
        "isoCode": "CC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cc.svg"
    },
    {
        "name": "Colombia",
        "dialCode": "+57",
        "isoCode": "CO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/co.svg"
    },
    {
        "name": "Comoros",
        "dialCode": "+269",
        "isoCode": "KM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/km.svg"
    },
    {
        "name": "Congo",
        "dialCode": "+242",
        "isoCode": "CG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cg.svg"
    },
    {
        "name": "Cook Islands",
        "dialCode": "+682",
        "isoCode": "CK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ck.svg"
    },
    {
        "name": "Costa Rica",
        "dialCode": "+506",
        "isoCode": "CR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cr.svg"
    },
    {
        "name": "Croatia",
        "dialCode": "+385",
        "isoCode": "HR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/hr.svg"
    },
    {
        "name": "Cuba",
        "dialCode": "+53",
        "isoCode": "CU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cu.svg"
    },
    {
        "name": "Cyprus",
        "dialCode": "+357",
        "isoCode": "CY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cy.svg"
    },
    {
        "name": "Czech Republic",
        "dialCode": "+420",
        "isoCode": "CZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cz.svg"
    },
    {
        "name": "Democratic Republic of the Congo",
        "dialCode": "+243",
        "isoCode": "CD",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/cd.svg"
    },
    {
        "name": "Denmark",
        "dialCode": "+45",
        "isoCode": "DK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/dk.svg"
    },
    {
        "name": "Djibouti",
        "dialCode": "+253",
        "isoCode": "DJ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/dj.svg"
    },
    {
        "name": "Dominica",
        "dialCode": "+1767",
        "isoCode": "DM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/dm.svg"
    },
    {
        "name": "Dominican Republic",
        "dialCode": "+1849",
        "isoCode": "DO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/do.svg"
    },
    {
        "name": "Ecuador",
        "dialCode": "+593",
        "isoCode": "EC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ec.svg"
    },
    {
        "name": "Egypt",
        "dialCode": "+20",
        "isoCode": "EG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/eg.svg"
    },
    {
        "name": "El Salvador",
        "dialCode": "+503",
        "isoCode": "SV",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sv.svg"
    },
    {
        "name": "Equatorial Guinea",
        "dialCode": "+240",
        "isoCode": "GQ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gq.svg"
    },
    {
        "name": "Eritrea",
        "dialCode": "+291",
        "isoCode": "ER",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/er.svg"
    },
    {
        "name": "Estonia",
        "dialCode": "+372",
        "isoCode": "EE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ee.svg"
    },
    {
        "name": "Eswatini",
        "dialCode": "+268",
        "isoCode": "SZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sz.svg"
    },
    {
        "name": "Ethiopia",
        "dialCode": "+251",
        "isoCode": "ET",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/et.svg"
    },
    {
        "name": "Falkland Islands (Malvinas)",
        "dialCode": "+500",
        "isoCode": "FK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/fk.svg"
    },
    {
        "name": "Faroe Islands",
        "dialCode": "+298",
        "isoCode": "FO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/fo.svg"
    },
    {
        "name": "Fiji",
        "dialCode": "+679",
        "isoCode": "FJ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/fj.svg"
    },
    {
        "name": "Finland",
        "dialCode": "+358",
        "isoCode": "FI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/fi.svg"
    },
    {
        "name": "France",
        "dialCode": "+33",
        "isoCode": "FR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/fr.svg"
    },
    {
        "name": "French Guiana",
        "dialCode": "+594",
        "isoCode": "GF",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gf.svg"
    },
    {
        "name": "French Polynesia",
        "dialCode": "+689",
        "isoCode": "PF",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pf.svg"
    },
    {
        "name": "Gabon",
        "dialCode": "+241",
        "isoCode": "GA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ga.svg"
    },
    {
        "name": "Gambia",
        "dialCode": "+220",
        "isoCode": "GM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gm.svg"
    },
    {
        "name": "Georgia",
        "dialCode": "+995",
        "isoCode": "GE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ge.svg"
    },
    {
        "name": "Germany",
        "dialCode": "+49",
        "isoCode": "DE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/de.svg"
    },
    {
        "name": "Ghana",
        "dialCode": "+233",
        "isoCode": "GH",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gh.svg"
    },
    {
        "name": "Gibraltar",
        "dialCode": "+350",
        "isoCode": "GI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gi.svg"
    },
    {
        "name": "Greece",
        "dialCode": "+30",
        "isoCode": "GR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gr.svg"
    },
    {
        "name": "Greenland",
        "dialCode": "+299",
        "isoCode": "GL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gl.svg"
    },
    {
        "name": "Grenada",
        "dialCode": "+1473",
        "isoCode": "GD",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gd.svg"
    },
    {
        "name": "Guadeloupe",
        "dialCode": "+590",
        "isoCode": "GP",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gp.svg"
    },
    {
        "name": "Guam",
        "dialCode": "+1671",
        "isoCode": "GU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gu.svg"
    },
    {
        "name": "Guatemala",
        "dialCode": "+502",
        "isoCode": "GT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gt.svg"
    },
    {
        "name": "Guernsey",
        "dialCode": "+44-1481",
        "isoCode": "GG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gg.svg"
    },
    {
        "name": "Guinea",
        "dialCode": "+224",
        "isoCode": "GN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gn.svg"
    },
    {
        "name": "Guinea-Bissau",
        "dialCode": "+245",
        "isoCode": "GW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gw.svg"
    },
    {
        "name": "Guyana",
        "dialCode": "+592",
        "isoCode": "GY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gy.svg"
    },
    {
        "name": "Haiti",
        "dialCode": "+509",
        "isoCode": "HT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ht.svg"
    },
    {
        "name": "Holy See (Vatican City State)",
        "dialCode": "+379",
        "isoCode": "VA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/va.svg"
    },
    {
        "name": "Honduras",
        "dialCode": "+504",
        "isoCode": "HN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/hn.svg"
    },
    {
        "name": "Hong Kong",
        "dialCode": "+852",
        "isoCode": "HK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/hk.svg"
    },
    {
        "name": "Hungary",
        "dialCode": "+36",
        "isoCode": "HU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/hu.svg"
    },
    {
        "name": "Iceland",
        "dialCode": "+354",
        "isoCode": "IS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/is.svg"
    },
    {
        "name": "India",
        "dialCode": "+91",
        "isoCode": "IN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/in.svg"
    },
    {
        "name": "Indonesia",
        "dialCode": "+62",
        "isoCode": "ID",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/id.svg"
    },
    {
        "name": "Iran",
        "dialCode": "+98",
        "isoCode": "IR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ir.svg"
    },
    {
        "name": "Iraq",
        "dialCode": "+964",
        "isoCode": "IQ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/iq.svg"
    },
    {
        "name": "Ireland",
        "dialCode": "+353",
        "isoCode": "IE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ie.svg"
    },
    {
        "name": "Isle of Man",
        "dialCode": "+44-1624",
        "isoCode": "IM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/im.svg"
    },
    {
        "name": "Israel",
        "dialCode": "+972",
        "isoCode": "IL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/il.svg"
    },
    {
        "name": "Italy",
        "dialCode": "+39",
        "isoCode": "IT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/it.svg"
    },
    {
        "name": "Ivory Coast / Cote d'Ivoire",
        "dialCode": "+225",
        "isoCode": "CI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ci.svg"
    },
    {
        "name": "Jamaica",
        "dialCode": "+1876",
        "isoCode": "JM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/jm.svg"
    },
    {
        "name": "Japan",
        "dialCode": "+81",
        "isoCode": "JP",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/jp.svg"
    },
    {
        "name": "Jersey",
        "dialCode": "+44-1534",
        "isoCode": "JE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/je.svg"
    },
    {
        "name": "Jordan",
        "dialCode": "+962",
        "isoCode": "JO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/jo.svg"
    },
    {
        "name": "Kazakhstan",
        "dialCode": "+77",
        "isoCode": "KZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/kz.svg"
    },
    {
        "name": "Kenya",
        "dialCode": "+254",
        "isoCode": "KE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ke.svg"
    },
    {
        "name": "Kiribati",
        "dialCode": "+686",
        "isoCode": "KI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ki.svg"
    },
    {
        "name": "Korea, Democratic People's Republic of Korea",
        "dialCode": "+850",
        "isoCode": "KP",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/kp.svg"
    },
    {
        "name": "Korea, Republic of South Korea",
        "dialCode": "+82",
        "isoCode": "KR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/kr.svg"
    },
    {
        "name": "Kosovo",
        "dialCode": "+383",
        "isoCode": "XK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/xk.svg"
    },
    {
        "name": "Kuwait",
        "dialCode": "+965",
        "isoCode": "KW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/kw.svg"
    },
    {
        "name": "Kyrgyzstan",
        "dialCode": "+996",
        "isoCode": "KG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/kg.svg"
    },
    {
        "name": "Laos",
        "dialCode": "+856",
        "isoCode": "LA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/la.svg"
    },
    {
        "name": "Latvia",
        "dialCode": "+371",
        "isoCode": "LV",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/lv.svg"
    },
    {
        "name": "Lebanon",
        "dialCode": "+961",
        "isoCode": "LB",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/lb.svg"
    },
    {
        "name": "Lesotho",
        "dialCode": "+266",
        "isoCode": "LS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ls.svg"
    },
    {
        "name": "Liberia",
        "dialCode": "+231",
        "isoCode": "LR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/lr.svg"
    },
    {
        "name": "Libya",
        "dialCode": "+218",
        "isoCode": "LY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ly.svg"
    },
    {
        "name": "Liechtenstein",
        "dialCode": "+423",
        "isoCode": "LI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/li.svg"
    },
    {
        "name": "Lithuania",
        "dialCode": "+370",
        "isoCode": "LT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/lt.svg"
    },
    {
        "name": "Luxembourg",
        "dialCode": "+352",
        "isoCode": "LU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/lu.svg"
    },
    {
        "name": "Macau",
        "dialCode": "+853",
        "isoCode": "MO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mo.svg"
    },
    {
        "name": "Madagascar",
        "dialCode": "+261",
        "isoCode": "MG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mg.svg"
    },
    {
        "name": "Malawi",
        "dialCode": "+265",
        "isoCode": "MW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mw.svg"
    },
    {
        "name": "Malaysia",
        "dialCode": "+60",
        "isoCode": "MY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/my.svg"
    },
    {
        "name": "Maldives",
        "dialCode": "+960",
        "isoCode": "MV",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mv.svg"
    },
    {
        "name": "Mali",
        "dialCode": "+223",
        "isoCode": "ML",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ml.svg"
    },
    {
        "name": "Malta",
        "dialCode": "+356",
        "isoCode": "MT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mt.svg"
    },
    {
        "name": "Marshall Islands",
        "dialCode": "+692",
        "isoCode": "MH",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mh.svg"
    },
    {
        "name": "Martinique",
        "dialCode": "+596",
        "isoCode": "MQ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mq.svg"
    },
    {
        "name": "Mauritania",
        "dialCode": "+222",
        "isoCode": "MR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mr.svg"
    },
    {
        "name": "Mauritius",
        "dialCode": "+230",
        "isoCode": "MU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mu.svg"
    },
    {
        "name": "Mayotte",
        "dialCode": "+262",
        "isoCode": "YT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/yt.svg"
    },
    {
        "name": "Mexico",
        "dialCode": "+52",
        "isoCode": "MX",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mx.svg"
    },
    {
        "name": "Micronesia, Federated States of Micronesia",
        "dialCode": "+691",
        "isoCode": "FM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/fm.svg"
    },
    {
        "name": "Moldova",
        "dialCode": "+373",
        "isoCode": "MD",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/md.svg"
    },
    {
        "name": "Monaco",
        "dialCode": "+377",
        "isoCode": "MC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mc.svg"
    },
    {
        "name": "Mongolia",
        "dialCode": "+976",
        "isoCode": "MN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mn.svg"
    },
    {
        "name": "Montenegro",
        "dialCode": "+382",
        "isoCode": "ME",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/me.svg"
    },
    {
        "name": "Montserrat",
        "dialCode": "+1664",
        "isoCode": "MS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ms.svg"
    },
    {
        "name": "Morocco",
        "dialCode": "+212",
        "isoCode": "MA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ma.svg"
    },
    {
        "name": "Mozambique",
        "dialCode": "+258",
        "isoCode": "MZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mz.svg"
    },
    {
        "name": "Myanmar",
        "dialCode": "+95",
        "isoCode": "MM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mm.svg"
    },
    {
        "name": "Namibia",
        "dialCode": "+264",
        "isoCode": "NA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/na.svg"
    },
    {
        "name": "Nauru",
        "dialCode": "+674",
        "isoCode": "NR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/nr.svg"
    },
    {
        "name": "Nepal",
        "dialCode": "+977",
        "isoCode": "NP",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/np.svg"
    },
    {
        "name": "Netherlands",
        "dialCode": "+31",
        "isoCode": "NL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/nl.svg"
    },
    {
        "name": "Netherlands Antilles",
        "dialCode": "+599",
        "isoCode": "AN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/an.svg"
    },
    {
        "name": "New Caledonia",
        "dialCode": "+687",
        "isoCode": "NC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/nc.svg"
    },
    {
        "name": "New Zealand",
        "dialCode": "+64",
        "isoCode": "NZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/nz.svg"
    },
    {
        "name": "Nicaragua",
        "dialCode": "+505",
        "isoCode": "NI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ni.svg"
    },
    {
        "name": "Niger",
        "dialCode": "+227",
        "isoCode": "NE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ne.svg"
    },
    {
        "name": "Nigeria",
        "dialCode": "+234",
        "isoCode": "NG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ng.svg"
    },
    {
        "name": "Niue",
        "dialCode": "+683",
        "isoCode": "NU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/nu.svg"
    },
    {
        "name": "Norfolk Island",
        "dialCode": "+672",
        "isoCode": "NF",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/nf.svg"
    },
    {
        "name": "North Macedonia",
        "dialCode": "+389",
        "isoCode": "MK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mk.svg"
    },
    {
        "name": "Northern Mariana Islands",
        "dialCode": "+1670",
        "isoCode": "MP",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mp.svg"
    },
    {
        "name": "Norway",
        "dialCode": "+47",
        "isoCode": "NO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/no.svg"
    },
    {
        "name": "Oman",
        "dialCode": "+968",
        "isoCode": "OM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/om.svg"
    },
    {
        "name": "Pakistan",
        "dialCode": "+92",
        "isoCode": "PK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pk.svg"
    },
    {
        "name": "Palau",
        "dialCode": "+680",
        "isoCode": "PW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pw.svg"
    },
    {
        "name": "Palestine",
        "dialCode": "+970",
        "isoCode": "PS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ps.svg"
    },
    {
        "name": "Panama",
        "dialCode": "+507",
        "isoCode": "PA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pa.svg"
    },
    {
        "name": "Papua New Guinea",
        "dialCode": "+675",
        "isoCode": "PG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pg.svg"
    },
    {
        "name": "Paraguay",
        "dialCode": "+595",
        "isoCode": "PY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/py.svg"
    },
    {
        "name": "Peru",
        "dialCode": "+51",
        "isoCode": "PE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pe.svg"
    },
    {
        "name": "Philippines",
        "dialCode": "+63",
        "isoCode": "PH",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ph.svg"
    },
    {
        "name": "Pitcairn",
        "dialCode": "+872",
        "isoCode": "PN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pn.svg"
    },
    {
        "name": "Poland",
        "dialCode": "+48",
        "isoCode": "PL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pl.svg"
    },
    {
        "name": "Portugal",
        "dialCode": "+351",
        "isoCode": "PT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pt.svg"
    },
    {
        "name": "Puerto Rico",
        "dialCode": "+1939",
        "isoCode": "PR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pr.svg"
    },
    {
        "name": "Qatar",
        "dialCode": "+974",
        "isoCode": "QA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/qa.svg"
    },
    {
        "name": "Reunion",
        "dialCode": "+262",
        "isoCode": "RE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/re.svg"
    },
    {
        "name": "Romania",
        "dialCode": "+40",
        "isoCode": "RO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ro.svg"
    },
    {
        "name": "Russia",
        "dialCode": "+7",
        "isoCode": "RU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ru.svg"
    },
    {
        "name": "Rwanda",
        "dialCode": "+250",
        "isoCode": "RW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/rw.svg"
    },
    {
        "name": "Saint Barthelemy",
        "dialCode": "+590",
        "isoCode": "BL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/bl.svg"
    },
    {
        "name": "Saint Helena, Ascension and Tristan Da Cunha",
        "dialCode": "+290",
        "isoCode": "SH",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sh.svg"
    },
    {
        "name": "Saint Kitts and Nevis",
        "dialCode": "+1869",
        "isoCode": "KN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/kn.svg"
    },
    {
        "name": "Saint Lucia",
        "dialCode": "+1758",
        "isoCode": "LC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/lc.svg"
    },
    {
        "name": "Saint Martin",
        "dialCode": "+590",
        "isoCode": "MF",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/mf.svg"
    },
    {
        "name": "Saint Pierre and Miquelon",
        "dialCode": "+508",
        "isoCode": "PM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/pm.svg"
    },
    {
        "name": "Saint Vincent and the Grenadines",
        "dialCode": "+1784",
        "isoCode": "VC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/vc.svg"
    },
    {
        "name": "Samoa",
        "dialCode": "+685",
        "isoCode": "WS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ws.svg"
    },
    {
        "name": "San Marino",
        "dialCode": "+378",
        "isoCode": "SM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sm.svg"
    },
    {
        "name": "Sao Tome and Principe",
        "dialCode": "+239",
        "isoCode": "ST",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/st.svg"
    },
    {
        "name": "Saudi Arabia",
        "dialCode": "+966",
        "isoCode": "SA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sa.svg"
    },
    {
        "name": "Senegal",
        "dialCode": "+221",
        "isoCode": "SN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sn.svg"
    },
    {
        "name": "Serbia",
        "dialCode": "+381",
        "isoCode": "RS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/rs.svg"
    },
    {
        "name": "Seychelles",
        "dialCode": "+248",
        "isoCode": "SC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sc.svg"
    },
    {
        "name": "Sierra Leone",
        "dialCode": "+232",
        "isoCode": "SL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sl.svg"
    },
    {
        "name": "Singapore",
        "dialCode": "+65",
        "isoCode": "SG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sg.svg"
    },
    {
        "name": "Sint Maarten",
        "dialCode": "+1721",
        "isoCode": "SX",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sx.svg"
    },
    {
        "name": "Slovakia",
        "dialCode": "+421",
        "isoCode": "SK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sk.svg"
    },
    {
        "name": "Slovenia",
        "dialCode": "+386",
        "isoCode": "SI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/si.svg"
    },
    {
        "name": "Solomon Islands",
        "dialCode": "+677",
        "isoCode": "SB",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sb.svg"
    },
    {
        "name": "Somalia",
        "dialCode": "+252",
        "isoCode": "SO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/so.svg"
    },
    {
        "name": "South Africa",
        "dialCode": "+27",
        "isoCode": "ZA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/za.svg"
    },
    {
        "name": "South Georgia and the South Sandwich Islands",
        "dialCode": "+500",
        "isoCode": "GS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gs.svg"
    },
    {
        "name": "South Sudan",
        "dialCode": "+211",
        "isoCode": "SS",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ss.svg"
    },
    {
        "name": "Spain",
        "dialCode": "+34",
        "isoCode": "ES",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/es.svg"
    },
    {
        "name": "Sri Lanka",
        "dialCode": "+94",
        "isoCode": "LK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/lk.svg"
    },
    {
        "name": "Sudan",
        "dialCode": "+249",
        "isoCode": "SD",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sd.svg"
    },
    {
        "name": "Suriname",
        "dialCode": "+597",
        "isoCode": "SR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sr.svg"
    },
    {
        "name": "Svalbard and Jan Mayen",
        "dialCode": "+47",
        "isoCode": "SJ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sj.svg"
    },
    {
        "name": "Sweden",
        "dialCode": "+46",
        "isoCode": "SE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/se.svg"
    },
    {
        "name": "Switzerland",
        "dialCode": "+41",
        "isoCode": "CH",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ch.svg"
    },
    {
        "name": "Syrian Arab Republic",
        "dialCode": "+963",
        "isoCode": "SY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/sy.svg"
    },
    {
        "name": "Taiwan",
        "dialCode": "+886",
        "isoCode": "TW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tw.svg"
    },
    {
        "name": "Tajikistan",
        "dialCode": "+992",
        "isoCode": "TJ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tj.svg"
    },
    {
        "name": "Tanzania, United Republic of Tanzania",
        "dialCode": "+255",
        "isoCode": "TZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tz.svg"
    },
    {
        "name": "Thailand",
        "dialCode": "+66",
        "isoCode": "TH",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/th.svg"
    },
    {
        "name": "Timor-Leste",
        "dialCode": "+670",
        "isoCode": "TL",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tl.svg"
    },
    {
        "name": "Togo",
        "dialCode": "+228",
        "isoCode": "TG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tg.svg"
    },
    {
        "name": "Tokelau",
        "dialCode": "+690",
        "isoCode": "TK",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tk.svg"
    },
    {
        "name": "Tonga",
        "dialCode": "+676",
        "isoCode": "TO",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/to.svg"
    },
    {
        "name": "Trinidad and Tobago",
        "dialCode": "+1868",
        "isoCode": "TT",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tt.svg"
    },
    {
        "name": "Tunisia",
        "dialCode": "+216",
        "isoCode": "TN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tn.svg"
    },
    {
        "name": "Turkey",
        "dialCode": "+90",
        "isoCode": "TR",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tr.svg"
    },
    {
        "name": "Turkmenistan",
        "dialCode": "+993",
        "isoCode": "TM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tm.svg"
    },
    {
        "name": "Turks and Caicos Islands",
        "dialCode": "+1649",
        "isoCode": "TC",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tc.svg"
    },
    {
        "name": "Tuvalu",
        "dialCode": "+688",
        "isoCode": "TV",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/tv.svg"
    },
    {
        "name": "Uganda",
        "dialCode": "+256",
        "isoCode": "UG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ug.svg"
    },
    {
        "name": "Ukraine",
        "dialCode": "+380",
        "isoCode": "UA",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ua.svg"
    },
    {
        "name": "United Arab Emirates",
        "dialCode": "+971",
        "isoCode": "AE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ae.svg"
    },
    {
        "name": "United Kingdom",
        "dialCode": "+44",
        "isoCode": "GB",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/gb.svg"
    },
    {
        "name": "United States",
        "dialCode": "+1",
        "isoCode": "US",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/us.svg"
    },
    {
        "name": "United States Minor Outlying Islands",
        "dialCode": "+246",
        "isoCode": "UMI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/umi.svg"
    },
    {
        "name": "Uruguay",
        "dialCode": "+598",
        "isoCode": "UY",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/uy.svg"
    },
    {
        "name": "Uzbekistan",
        "dialCode": "+998",
        "isoCode": "UZ",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/uz.svg"
    },
    {
        "name": "Vanuatu",
        "dialCode": "+678",
        "isoCode": "VU",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/vu.svg"
    },
    {
        "name": "Venezuela, Bolivarian Republic of Venezuela",
        "dialCode": "+58",
        "isoCode": "VE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ve.svg"
    },
    {
        "name": "Vietnam",
        "dialCode": "+84",
        "isoCode": "VN",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/vn.svg"
    },
    {
        "name": "Virgin Islands, British",
        "dialCode": "+1284",
        "isoCode": "VG",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/vg.svg"
    },
    {
        "name": "Virgin Islands, U.S.",
        "dialCode": "+1340",
        "isoCode": "VI",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/vi.svg"
    },
    {
        "name": "Wallis and Futuna",
        "dialCode": "+681",
        "isoCode": "WF",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/wf.svg"
    },
    {
        "name": "Yemen",
        "dialCode": "+967",
        "isoCode": "YE",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/ye.svg"
    },
    {
        "name": "Zambia",
        "dialCode": "+260",
        "isoCode": "ZM",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/zm.svg"
    },
    {
        "name": "Zimbabwe",
        "dialCode": "+263",
        "isoCode": "ZW",
        "flag": "https://cdn.kcak11.com/CountryFlags/countries/zw.svg"
    }
]*/

const TELEPHONE_CODES = [
    ["name" => 'UK (+44)', "code" => '44'],
    ["name" => 'USA (+1)', "code" => '1'],
    ["name" => 'Algeria (+213)', "code" => '213'],
    ["name" => 'Andorra (+376)', "code" => '376'],
    ["name" => 'Angola (+244)', "code" => '244'],
    ["name" => 'Anguilla (+1264)', "code" => '1264'],
    ["name" => 'Antigua & Barbuda (+1268)', "code" => '1268'],
    ["name" => 'Argentina (+54)', "code" => '54'],
    ["name" => 'Armenia (+374)', "code" => '374'],
    ["name" => 'Aruba (+297)', "code" => '297'],
    ["name" => 'Australia (+61)', "code" => '61'],
    ["name" => 'Austria (+43)', "code" => '43'],
    ["name" => 'Azerbaijan (+994)', "code" => '994'],
    ["name" => 'Bahamas (+1242)', "code" => '1242'],
    ["name" => 'Bahrain (+973)', "code" => '973'],
    ["name" => 'Bangladesh (+880)', "code" => '880'],
    ["name" => 'Barbados (+1246)', "code" => '1246'],
    ["name" => 'Belarus (+375)', "code" => '375'],
    ["name" => 'Belgium (+32)', "code" => '32'],
    ["name" => 'Belize (+501)', "code" => '501'],
    ["name" => 'Benin (+229)', "code" => '229'],
    ["name" => 'Bermuda (+1441)', "code" => '1441'],
    ["name" => 'Bhutan (+975)', "code" => '975'],
    ["name" => 'Bolivia (+591)', "code" => '591'],
    ["name" => 'Bosnia Herzegovina (+387)', "code" => '387'],
    ["name" => 'Botswana (+267)', "code" => '267'],
    ["name" => 'Brazil (+55)', "code" => '55'],
    ["name" => 'Brunei (+673)', "code" => '673'],
    ["name" => 'Bulgaria (+359)', "code" => '359'],
    ["name" => 'Burkina Faso (+226)', "code" => '226'],
    ["name" => 'Burundi (+257)', "code" => '257'],
    ["name" => 'Cambodia (+855)', "code" => '855'],
    ["name" => 'Cameroon (+237)', "code" => '237'],
    ["name" => 'Canada (+1)', "code" => '1'],
    ["name" => 'Cape Verde Islands (+238)', "code" => '238'],
    ["name" => 'Cayman Islands (+1345)', "code" => '1345'],
    ["name" => 'Central African Republic (+236)', "code" => '236'],
    ["name" => 'Chile (+56)', "code" => '56'],
    ["name" => 'China (+86)', "code" => '86'],
    ["name" => 'Colombia (+57)', "code" => '57'],
    ["name" => 'Comoros (+269)', "code" => '269'],
    ["name" => 'Congo (+242)', "code" => '242'],
    ["name" => 'Cook Islands (+682)', "code" => '682'],
    ["name" => 'Costa Rica (+506)', "code" => '506'],
    ["name" => 'Croatia (+385)', "code" => '385'],
    ["name" => 'Cuba (+53)', "code" => '53'],
    ["name" => 'Cyprus North (+90392)', "code" => '90392'],
    ["name" => 'Cyprus South (+357)', "code" => '357'],
    ["name" => 'Czech Republic (+42)', "code" => '42'],
    ["name" => 'Denmark (+45)', "code" => '45'],
    ["name" => 'Djibouti (+253)', "code" => '253'],
    ["name" => 'Dominica (+1767)', "code" => '1767'],
    ["name" => 'Dominican Republic (+1809)', "code" => '1809'],
    ["name" => 'Ecuador (+593)', "code" => '593'],
    ["name" => 'Egypt (+20)', "code" => '20'],
    ["name" => 'El Salvador (+503)', "code" => '503'],
    ["name" => 'Equatorial Guinea (+240)', "code" => '240'],
    ["name" => 'Eritrea (+291)', "code" => '291'],
    ["name" => 'Estonia (+372)', "code" => '372'],
    ["name" => 'Ethiopia (+251)', "code" => '251'],
    ["name" => 'Falkland Islands (+500)', "code" => '500'],
    ["name" => 'Faroe Islands (+298)', "code" => '298'],
    ["name" => 'Fiji (+679)', "code" => '679'],
    ["name" => 'Finland (+358)', "code" => '358'],
    ["name" => 'France (+33)', "code" => '33'],
    ["name" => 'French Guiana (+594)', "code" => '594'],
    ["name" => 'French Polynesia (+689)', "code" => '689'],
    ["name" => 'Gabon (+241)', "code" => '241'],
    ["name" => 'Gambia (+220)', "code" => '220'],
    ["name" => 'Georgia (+7880)', "code" => '7880'],
    ["name" => 'Germany (+49)', "code" => '49'],
    ["name" => 'Ghana (+233)', "code" => '233'],
    ["name" => 'Gibraltar (+350)', "code" => '350'],
    ["name" => 'Greece (+30)', "code" => '30'],
    ["name" => 'Greenland (+299)', "code" => '299'],
    ["name" => 'Grenada (+1473)', "code" => '1473'],
    ["name" => 'Guadeloupe (+590)', "code" => '590'],
    ["name" => 'Guam (+671)', "code" => '671'],
    ["name" => 'Guatemala (+502)', "code" => '502'],
    ["name" => 'Guinea (+224)', "code" => '224'],
    ["name" => 'Guinea - Bissau (+245)', "code" => '245'],
    ["name" => 'Guyana (+592)', "code" => '592'],
    ["name" => 'Haiti (+509)', "code" => '509'],
    ["name" => 'Honduras (+504)', "code" => '504'],
    ["name" => 'Hong Kong (+852)', "code" => '852'],
    ["name" => 'Hungary (+36)', "code" => '36'],
    ["name" => 'Iceland (+354)', "code" => '354'],
    ["name" => 'India (+91)', "code" => '91'],
    ["name" => 'Indonesia (+62)', "code" => '62'],
    ["name" => 'Iran (+98)', "code" => '98'],
    ["name" => 'Iraq (+964)', "code" => '964'],
    ["name" => 'Ireland (+353)', "code" => '353'],
    ["name" => 'Israel (+972)', "code" => '972'],
    ["name" => 'Italy (+39)', "code" => '39'],
    ["name" => 'Jamaica (+1876)', "code" => '1876'],
    ["name" => 'Japan (+81)', "code" => '81'],
    ["name" => 'Jordan (+962)', "code" => '962'],
    ["name" => 'Kazakhstan (+7)', "code" => '7'],
    ["name" => 'Kenya (+254)', "code" => '254'],
    ["name" => 'Kiribati (+686)', "code" => '686'],
    ["name" => 'Korea North (+850)', "code" => '850'],
    ["name" => 'Korea South (+82)', "code" => '82'],
    ["name" => 'Kuwait (+965)', "code" => '965'],
    ["name" => 'Kyrgyzstan (+996)', "code" => '996'],
    ["name" => 'Laos (+856)', "code" => '856'],
    ["name" => 'Latvia (+371)', "code" => '371'],
    ["name" => 'Lebanon (+961)', "code" => '961'],
    ["name" => 'Lesotho (+266)', "code" => '266'],
    ["name" => 'Liberia (+231)', "code" => '231'],
    ["name" => 'Libya (+218)', "code" => '218'],
    ["name" => 'Liechtenstein (+417)', "code" => '417'],
    ["name" => 'Lithuania (+370)', "code" => '370'],
    ["name" => 'Luxembourg (+352)', "code" => '352'],
    ["name" => 'Macao (+853)', "code" => '853'],
    ["name" => 'Macedonia (+389)', "code" => '389'],
    ["name" => 'Madagascar (+261)', "code" => '261'],
    ["name" => 'Malawi (+265)', "code" => '265'],
    ["name" => 'Malaysia (+60)', "code" => '60'],
    ["name" => 'Maldives (+960)', "code" => '960'],
    ["name" => 'Mali (+223)', "code" => '223'],
    ["name" => 'Malta (+356)', "code" => '356'],
    ["name" => 'Marshall Islands (+692)', "code" => '692'],
    ["name" => 'Martinique (+596)', "code" => '596'],
    ["name" => 'Mauritania (+222)', "code" => '222'],
    ["name" => 'Mayotte (+269)', "code" => '269'],
    ["name" => 'Mexico (+52)', "code" => '52'],
    ["name" => 'Micronesia (+691)', "code" => '691'],
    ["name" => 'Moldova (+373)', "code" => '373'],
    ["name" => 'Monaco (+377)', "code" => '377'],
    ["name" => 'Montserrat (+1664)', "code" => '1664'],
    ["name" => 'Morocco (+212)', "code" => '212'],
    ["name" => 'Mozambique (+258)', "code" => '258'],
    ["name" => 'Myanmar (+95)', "code" => '95'],
    ["name" => 'Namibia (+264)', "code" => '264'],
    ["name" => 'Nauru (+674)', "code" => '674'],
    ["name" => 'Nepal (+977)', "code" => '977'],
    ["name" => 'Netherlands (+31)', "code" => '31'],
    ["name" => 'New Caledonia (+687)', "code" => '687'],
    ["name" => 'New Zealand (+64)', "code" => '64'],
    ["name" => 'Nicaragua (+505)', "code" => '505'],
    ["name" => 'Niger (+227)', "code" => '227'],
    ["name" => 'Nigeria (+234)', "code" => '234'],
    ["name" => 'Niue (+683)', "code" => '683'],
    ["name" => 'Norfolk Islands (+672)', "code" => '672'],
    ["name" => 'Northern Marianas (+670)', "code" => '670'],
    ["name" => 'Norway (+47)', "code" => '47'],
    ["name" => 'Oman (+968)', "code" => '968'],
    ["name" => 'Palau (+680)', "code" => '680'],
    ["name" => 'Panama (+507)', "code" => '507'],
    ["name" => 'Papua New Guinea (+675)', "code" => '675'],
    ["name" => 'Paraguay (+595)', "code" => '595'],
    ["name" => 'Peru (+51)', "code" => '51'],
    ["name" => 'Philippines (+63)', "code" => '63'],
    ["name" => 'Poland (+48)', "code" => '48'],
    ["name" => 'Portugal (+351)', "code" => '351'],
    ["name" => 'Qatar (+974)', "code" => '974'],
    ["name" => 'Reunion (+262)', "code" => '262'],
    ["name" => 'Romania (+40)', "code" => '40'],
    ["name" => 'Russia (+7)', "code" => '7'],
    ["name" => 'Rwanda (+250)', "code" => '250'],
    ["name" => 'San Marino (+378)', "code" => '378'],
    ["name" => 'Sao Tome & Principe (+239)', "code" => '239'],
    ["name" => 'Saudi Arabia (+966)', "code" => '966'],
    ["name" => 'Senegal (+221)', "code" => '221'],
    ["name" => 'Serbia (+381)', "code" => '381'],
    ["name" => 'Seychelles (+248)', "code" => '248'],
    ["name" => 'Sierra Leone (+232)', "code" => '232'],
    ["name" => 'Singapore (+65)', "code" => '65'],
    ["name" => 'Slovak Republic (+421)', "code" => '421'],
    ["name" => 'Slovenia (+386)', "code" => '386'],
    ["name" => 'Solomon Islands (+677)', "code" => '677'],
    ["name" => 'Somalia (+252)', "code" => '252'],
    ["name" => 'South Africa (+27)', "code" => '27'],
    ["name" => 'Spain (+34)', "code" => '34'],
    ["name" => 'Sri Lanka (+94)', "code" => '94'],
    ["name" => 'St. Helena (+290)', "code" => '290'],
    ["name" => 'St. Kitts (+1869)', "code" => '1869'],
    ["name" => 'St. Lucia (+1758)', "code" => '1758'],
    ["name" => 'Sudan (+249)', "code" => '249'],
    ["name" => 'Suriname (+597)', "code" => '597'],
    ["name" => 'Swaziland (+268)', "code" => '268'],
    ["name" => 'Sweden (+46)', "code" => '46'],
    ["name" => 'Switzerland (+41)', "code" => '41'],
    ["name" => 'Syria (+963)', "code" => '963'],
    ["name" => 'Taiwan (+886)', "code" => '886'],
    ["name" => 'Tajikstan (+7)', "code" => '7'],
    ["name" => 'Thailand (+66)', "code" => '66'],
    ["name" => 'Togo (+228)', "code" => '228'],
    ["name" => 'Tonga (+676)', "code" => '676'],
    ["name" => 'Trinidad & Tobago (+1868)', "code" => '1868'],
    ["name" => 'Tunisia (+216)', "code" => '216'],
    ["name" => 'Turkey (+90)', "code" => '90'],
    ["name" => 'Turkmenistan (+7)', "code" => '7'],
    ["name" => 'Turkmenistan (+993)', "code" => '993'],
    ["name" => 'Turks & Caicos Islands (+1649)', "code" => '1649'],
    ["name" => 'Tuvalu (+688)', "code" => '688'],
    ["name" => 'Uganda (+256)', "code" => '256'],
    ["name" => 'Ukraine (+380)', "code" => '380'],
    ["name" => 'United Arab Emirates (+971)', "code" => '971'],
    ["name" => 'Uruguay (+598)', "code" => '598'],
    ["name" => 'Uzbekistan (+7)', "code" => '7'],
    ["name" => 'Vanuatu (+678)', "code" => '678'],
    ["name" => 'Vatican City (+379)', "code" => '379'],
    ["name" => 'Venezuela (+58)', "code" => '58'],
    ["name" => 'Vietnam (+84)', "code" => '84'],
    ["name" => 'Virgin Islands - British (+1284)', "code" => '1284'],
    ["name" => 'Virgin Islands - US (+1340)', "code" => '1340'],
    ["name" => 'Wallis & Futuna (+681)', "code" => '681'],
    ["name" => 'Yemen (North)(+969)', "code" => '969'],
    ["name" => 'Yemen (South)(+967)', "code" => '967'],
    ["name" => 'Zambia (+260)', "code" => '260'],
    ["name" => 'Zimbabwe (+263)', "code" => '263'],
];
