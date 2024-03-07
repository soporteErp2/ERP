<?php
error_reporting(0);
/**
 * yaIndexer - yet another index generator tool simple and easy to use.
 * This script allows you to create simple and well looking indexes for directories
 * when this future is disabled by server or you just can't stand this ulgy
 * Apache/Lighttpd/whatever index look.<br><br>
 * To install script just copy it to directory of your choice and type in browser
 * http://server.name/path/to/script/indexer.php. You can change the name of
 * script to index.php and then you can just type http://server.name/path/to/script/<br>
 * <br>
 * I've been using a great set of mini icons created by Mark James, you can find 
 * realted information on his {@link http://www.famfamfam.com/lab/icons/silk/ page}
 * <br><br>
 * <b>Configuration</b><br>
 * There is only two things, that you may/want to configure, first is if you want
 * hide hidden files (in *nix way, starting by a dot), then set a HIDE_HIDDEN static
 * and if you want to see a short summary of curent directory under the tables of
 * files.<br><br>
 * <b>Contact with me:</b><br>
 * mail: alkemic7 (at) gmail (dot) com<br>
 * jid: alkemic7 (at) gmail (dot) com<br>
 *
 * @author Daniel Alkemic Czuba <alkemic7 (at) gmail (dot) com>
 * @version 1.0
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-Noncommercial-Share Alike 3.0 Unported
 * @package yaIndexer
 *
 */


/* CONFIGURATION STARTS BELOW */
/**
 * Do you want to hide hidden files in directories (hidden in *nix way, started with a dot)
 * Value: true or false
 */
define( "HIDE_HIDDEN", false );
/**
 * Do you want to see short summary under the main table with index
 * Value: true or false
 */
define( "SHOW_SUMMARY", true );

/* CONFIGURATION ENDS HERE */


/**
 * Script version.
 */
define( "YAI_VERSION", "1.0" );

if( isSet( $_GET["icon"] ) ){ // jesli zazadano wyswitlenia ikony

header("Content-type: image/png "); // wysylamy naglowek (.png)
/**
 * Array containing base64 coded icons.
 * Those icons are created by Mark James.
 * @global array $icons
 * @license http://creativecommons.org/licenses/by/2.5/ Creative Commons Attribution 2.5 License
 * @copyright http://www.famfamfam.com/lab/icons/silk/
 */ 
$icons["dir"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAGrSURBVDjLxZO7ihRBFIa/6u0ZW7GHBUV0UQQTZz".
"d3QdhMQxOfwMRXEANBMNQX0MzAzFAwEzHwARbNFDdwEd31Mj3X7a6uOr9BtzNjYjKBJ6nicP7v3KqcJFaxhBVt".
"ZUAK8OHlld2st7Xl3DJPVONP+zEUV4HqL5UDYHr5xvuQAjgl/Qs7TzvOOVAjxjlC+ePSwe6DfbVegLVuT4r14e".
"Tr6zvA8xSAoBLzx6pvj4l+DZIezuVkG9fY2H7YRQIMZIBwycmzH1/s3F8AapfIPNF3kQk7+kw9PWBy+IZOdg5U".
"g3mkAATy/t0usovzGeCUWTjCz0B+Sj0ekfdvkZ3abBv+U4GaCtJ1iEm6ANQJ6fEzrG/engcKw/wXQvEKxSEKQx".
"RGKE7Izt+DSiwBJMUSm71rguMYhQKrBygOIRStf4TiFFRBvbRGKiQLWP29yRSHKBTtfdBmHs0BUpgvtgF4yRFR".
"+NUKi0XZcYjCeCG2smkzLAHkbRBmP0/Uk26O5YnUActBp1GsAI+S5nRJJJal5K1aAMrq0d6Tm9uI6zjyf75dAe".
"6tx/SsWeD//o2/Ab6IH3/h25pOAAAAAElFTkSuQmCC";
$icons["up"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0R".
"Vh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEGSURBVDjLpZM/LwRRFMXPspmEaGc1shHRaiXsJ5G".
"IRixbCr6SikxIlqgJM5UohIiGdofovHf/PZVmYwZvTntPfjnn3txWCAFNNFE33L/ZKXYv+1dRgL3r7bu0PbucJ".
"p3e4GLjtsrXGq9wkA8SU7tPk87i/MwCzAyP5QNeytcnJl46XMuoNoGKDoVlTkQhJpAgmJqcBjnqkqPTXxN8qz9".
"cD6vdHtQMxXOBt49y5XjzLB/3tau6kWewKiwoRu8jZFvn+U++GgCBlWFBQY4qr1ANcAQxgQaFjwH4TwYrQ5skY".
"BOYKbzjiASOwCrNd2BBwZ4jAcowGJgkAuAZ2dEJhAUqij//wn/1BesSumImTttSAAAAAElFTkSuQmCC";
$icons["default"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAA".
"ABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAC4SURBVCjPdZFbDsIgEEWnrsMm7oGGfZrohx".
"vU+Iq1TyjU60Bf1pac4Yc5YS4ZAtGWBMk/drQBOVwJlZrWYkLhsB8UV9K0BUrPGy9cWbng2CtEEUmLGppPjRwp".
"bixUKHBiZRS0p+ZGhvs4irNEvWD8heHpbsyDXznPhYFOyTjJc13olIqzZCHBouE0FRMUjA+s1gTjaRgVFpqRwC".
"8mfoXPPEVPS7LbRaJL2y7bOifRCTEli3U7BMWgLzKlW/CuebZPAAAAAElFTkSuQmCC";
$icons["php"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAGsSURBVDjLjZNLSwJRFICtFv2AgggS2vQLDFvVpn".
"0Pi4iItm1KItvWJqW1pYsRemyyNILARbZpm0WtrJ0kbmbUlHmr4+t0z60Z7oSSAx935txzvrlPBwA4EPKMEVwE".
"9z+ME/qtOkbgqtVqUqPRaDWbTegE6YdQKBRkJazAjcWapoGu6xayLIMoilAoFKhEEAQIh8OWxCzuQwEmVKtVMA".
"yDtoiqqiBJEhSLRSqoVCqAP+E47keCAvfU5sDQ8MRs/OYNtr1x2PXdwuJShLLljcFlNAW5HA9khLYp0TUhSYML".
"Hm7PLEDS7zyw3ybRqyfg+TyBtwl2sDP1nKWFiUSazFex3tk45sXjL1Aul20CGTs+syVY37igBbwg03eMsfH9gw".
"SsrZ+Doig2QZsdNiZmMkVrKmwc18azHKELyQrOMEHTDJp8HXu1hostG8dY8PiRngdWMEq467ZwbDxwlIR8XrQL".
"cBvn5k9Gpmd8fn/gHlZWT20C/D4k8eTDB3yVFKjX6xSbgD1If8G970Q3QbvbPehAyxL8SibJEdaxo5dikqvS28".
"sInCjp4Tqb4NV3fgPirZ4pD4KS4wAAAABJRU5ErkJggg==";
$icons["zip"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAALnSURBVDjLfZNLaFx1HIW/e2fuzJ00w0ymkpQpiU".
"KfMT7SblzU4kayELEptRChUEFEqKALUaRUV2YhlCLYjYq4FBeuiqZgC6FIQzBpEGpDkzHNs5PMTJtmHnfu6//7".
"uSh2IYNnffg23zmWqtIpd395YwiRL1Q0qyIfD56cmOvUs/4LWJg40auiH6jI+7v3ncybdo2Hy9ebKvqNGrn03N".
"j1+x0Bi1dHHVV9W0U+ye4d2d83+Ca2GJrlGZx0gkppkkfrsysqclFFvh8++3v7CWDh6ugIohfSPcPH+w6fwu05".
"ABoSby9yb3Kc/mePYXc9TdCqslWapVGdn1Zjxo++O33Fujtx4gdEzj61f8xyC8/jN2rsVOcxYZOoVSZtBewZOA".
"T+NonuAWw3S728wFZpFm975cekGjlz8NXLVtSo0SxPImGdtFfFq5epr21wdOxrnMwuaC2jrRJWfYHdxRfIFeDW".
"r0unkyrSUqxcyk2TLQzQrt6hqydPvidDBg/8VTAp8DegvYa3OU1z+SbuM6dQI62kioAAVgondwAnncWvzCDNCk".
"4CLO9vsJVw8xqN+iPiTB5SaTSKURGSaoTHHgxoAMlduL1HiFMZXP8BsvkbO1GD2O3GpLOIF0KsSBijxmCrMY+F".
"qgGJQDzQgGT3XrJ7DuI5EKZd4iDG+CHG84m8AIki1Ai2imRsx4FEBtQHCUB8MG1wi8QKGhjEC4mbAVHTx8kNYS".
"uoiGurkRtLN76ivb0K6SIkusCEoBEgaCQYPyT2QhKpAXKHTiMmQ2lmChWZTrw32v9TsLOyVlu8Nhi2G4Vs32Hs".
"TC9IA2KPRuU2Erp097+O5RRYvz3H1r3JldivfY7IR0+mfOu7l3pV5EM1cq744mi+OPwaRD71tSk0Vsp3/uLB6s".
"2minyrIpeOf7a00fFMf1w+MqRGzqvIW/teecdqV5a5P/8ncXv9ZxUdf/lCae5/3/hvpi4OjajIp4ikVOTLY+cX".
"r3Tq/QPcssKNXib9yAAAAABJRU5ErkJggg==";
$icons["pdf"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAHhSURBVDjLjZPLSxtRFIfVZRdWi0oFBf+BrhRx5d".
"KVYKG4tLhRqlgXPmIVJQiC60JCCZYqFHQh7rrQlUK7aVUUfCBRG5RkJpNkkswrM5NEf73n6gxpHujAB/fOvefj".
"nHM5VQCqCPa1MNoZnU/Qxqhx4woE7ZZlpXO53F0+n0c52Dl8Pt/nQkmhoJOCdUWBsvQJ2u4ODMOAwvapVAqSJH".
"GJKIrw+/2uxAmuJgFdMDUVincSxvEBTNOEpmlIp9OIxWJckMlkoOs6AoHAg6RYYNs2kp4RqOvfuIACVFVFPB4v".
"KYn3pFjAykDSOwVta52vqW6nlEQiwTMRBKGygIh9GEDCMwZH6EgoE+qHLMuVBdbfKwjv3yE6Ogjz/PQ/CZVDPS".
"FRRYE4/RHy1y8wry8RGWGSqyC/nM1meX9IQpQV2JKIUH8vrEgYmeAFwuPDCHa9QehtD26HBhCZnYC8ucGzKSsI".
"L8wgsjiH1PYPxL+vQvm5B/3sBMLyIm7GhhCe90BaWykV/Gp+VR9oqPVe9vfBTsruM1HtBKVPmFIUNusBrV3B4e".
"v6bsbyXlPdkbr/u+StHUkxruBPY+0KY8f38oWX/byvNAdluHNLeOxDB+uyQQfPCWZ3NT69BYJWkjxjnB1o9Fv/".
"ASQ5s+ABz8i2AAAAAElFTkSuQmCC";
$icons["txt"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADoSURBVBgZBcExblNBGAbA2ceegTRBuIKOgiihSZ".
"NTcC5LUHAihNJR0kGKCDcYJY6D3/77MdOinTvzAgCw8ysThIvn/VojIyMjIyPP+bS1sUQIV2s95pBDDvmbP/md".
"kft83tpYguZq5Jh/OeaYh+yzy8hTHvNlaxNNczm+la9OTlar1UdA/+C2A4trRCnD3jS8BB1obq2Gk6GU6QbQAS".
"4BUaYSQAf4bhhKKTFdAzrAOwAxEUAH+KEM01SY3gM6wBsEAQB0gJ+maZoC3gI6iPYaAIBJsiRmHU0AALOeFC3a".
"K2cWAACUXe7+AwO0lc9eTHYTAAAAAElFTkSuQmCC";
$icons["doc"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIdSURBVDjLjZO7a5RREMV/9/F9yaLBzQY3CC7EpB".
"GxU2O0EBG0sxHBUitTWYitYCsiiJL0NvlfgoWSRpGA4IMsm43ZXchmv8e9MxZZN1GD5MCBW8yce4aZY1QVAGPM".
"aWAacPwfm8A3VRUAVJWhyIUsy7plWcYQgh7GLMt0aWnpNTADWFX9Q2C+LMu4s7Oj/X5/xF6vp51OR1utloYQtN".
"ls6vLy8kjE3Huz9qPIQjcUg/GZenVOokIEiSBBCKUSQ+TFwwa1Wo2iKBARVlZW3iwuLr7izssPnwZ50DLIoWz9".
"zPT+s/fabrf/GQmY97GIIXGWp28/08si5+oV1jcGTCSO6nHH2pddYqmkaUq320VECCFQr9cBsBIVBbJcSdXQmK".
"7Q6Qsnq54sj2gBplS896RpSpIkjI2NjVZitdh7jAOSK6trXcpC2GjlfP1esHD+GDYozjm893jvSZJkXyAWe+ss".
"c6W5G9naLqkaw/pGxBrl1tVpJCrWWpxzI6GRgOQKCv2BYHPl5uUatROeSsVy7eIkU9UUiYoxBgDnHNbagw4U6y".
"AWwpmphNvXT6HAhAZuLNRx1iDDWzHG/L6ZEbyJVLa2c54/PgsKgyzw5MHcqKC9nROK/aaDvwN4KYS7j959DHk2".
"PtuYnBUBFUEVVBQRgzX7I/wNM7RmgEshhFXAcDSI9/6KHQZKAYkxDgA5SnOMcReI5kCcG8M42yM6iMDmL261ea".
"OOnqrOAAAAAElFTkSuQmCC";
$icons["pic"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAH8SURBVDjLjZPLaxNRFIfHLrpx10WbghXxH7DQx6".
"p14cadiCs31Y2LLizYhdBFWyhYaFUaUxLUQFCxL61E+0gofWGLRUqGqoWp2JpGG8g4ybTJJJm86897Ls4QJIm9".
"8DED9/6+mXNmjiAIwhlGE6P1P5xjVAEQiqHVlMlkYvl8/rhQKKAUbB92u91WSkKrlcLJZBK6rptomoZoNApFUb".
"hElmU4HA4u8YzU1PsmWryroxYrF9CBdDqNbDbLr0QikUAsFkM4HOaCVCoFesjzpwMuaeXuthYcw4rtvG4KKGxA".
"AgrE43FEIhGzlJQWxE/RirQ6i8/T7XjXV2szBawM8yDdU91GKaqqInQgwf9xCNmoB7LYgZn+Oud0T121KfiXYo".
"kqf8X+5jAyR3NQvtzEq96z4os7lhqzieW6TxJN3UVg8yEPqzu38P7xRVy+cPoay52qKDhUf0HaWsC3xRvstd3Q".
"vt9mTWtEOPAJf/+L8oKAfwfLnil43z7Bkusqdr2X4Btvg1+c5fsVBZJ/H9aXbix/2EAouAVx4zVmHl2BtOrkPa".
"ko2DsIwulexKhnG/cmfbg+uIbukXkooR/I5XKcioLu+8/QNTyGzqE36OidQNeDJayLe7yZBuUEv8t9iRIcU6Z4".
"FprZ36fTxknC7GyCBrBY0ECSE4yzAY1+gyH4Ay9cw2Ifwv9mAAAAAElFTkSuQmCC";
$icons["htm"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJwSURBVDjLjZPdT1JhHMetvyO3/gfLKy+68bLV2q".
"IAq7UyG6IrdRPL5hs2U5FR0MJIAqZlh7BVViI1kkyyiPkCyUtztQYTYbwJE8W+Pc8pjofK1dk+OxfP+X3O83sr".
"AVBCIc8eQhmh/B/sJezm4niCsvX19cTm5uZWPp/H3yDnUKvVKr6ELyinwWtra8hkMhzJZBLxeBwrKyusJBwOQ6".
"PRcJJC8K4DJ/dXM04DOswNqNOLybsRo9N6LCy7kUgkEIlEWEE2mwX9iVar/Smhglqd8IREKwya3qhg809gPLgI".
"/XsrOp/IcXVMhqnFSayurv6RElsT6ZCoov5u1fzUVwvcKRdefVuEKRCA3OFHv2MOxtlBdFuaMf/ZhWg0yt4kFA".
"oVCZS3Hd1gkpOwRt9h0LOES3YvamzPcdF7A6rlPrSbpbhP0kmlUmw9YrHYtoDku2T6pEZ/2ICXEQ8kTz+g2TkN".
"ceAKKv2nIHachn6qBx1MI5t/Op1mRXzBd31AiRafBp1vZyEcceGCzQ6p24yjEzocGT6LUacS0iExcrkcK6Fsp6".
"AXLRnmFOjyPMIZixPHmAAOGxZQec2OQyo7zpm6cNN6GZ2kK1RAofPAr8GA4oUMrdNNkIw/wPFhDwSjX3Dwlg0C".
"Qy96HreiTlcFZsaAjY0NNvh3QUXtHeHcoKMNA7NjqLd8xHmzDzXDRvRO1KHtngTyhzL4SHeooAAnKMxBtUYQbG".
"Wa0Dc+AsWzSVy3qkjeItLCFsz4XoNMaRFFAm4SyTXbmQa2YHQSGacR/pAXO+zGFif4JdlHCpShBzstEz+YfJtm".
"t5cnKKWS/1jnAnT1S38AGTynUFUTzJcAAAAASUVORK5CYII=";
$icons["c"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RV".
"h0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAHdSURBVDjLjZNLS+NgFIad+R0KwuzcSQddunTWXraK".
"A4KCuFKcWYqgVbE4TKJWNyqC2oHKoDBeEBF04UpFUVQqUoemSVOTJr2lrb5+5xsTUy+jgYdc3yfnnOQrAVBCsK".
"2U4WFUvUE546OTcwk82WxWz+fzt4VCAS/B7kMQhB9uiVtQReFkMolUKuWQSCSgaRpkWeYSSZIgiqIjscMfSEAP".
"ZDIZWJbF94RpmtB1HYqicEE6nQa9xO/3/5OQoM57/qm2a3PGtyzDtxzF/FYMe6c6F1DAMAzEYrFnLfGZ1A9dev".
"qC8o2wpmL8jwJhRcbw7ygGAxJYS7xvuxVVVXklkUjkUdAshgP+DRVfureXbPPcuoKe2b/QDKtIQpXQPOLx+KOg".
"f0nGCCu9smHiu7u8IGuDBHRsS6gdmgmJHEHfLwn9wSgqagc6Xvt8RC6X48MlCeEI2ibDIS8TVDYGBHfAO3ONow".
"vTOacqSEBQNY6gpvOkp3cxgq8/Q8ZxyISWsDAwfY32sSscnhk8SFAFBIWLBPQZq1sOvjX5LozOqTBaxSu0jF5i".
"YVV+FnZTJLB/pN0DDTv7WlHvtuQpLwrYxbv/DfIJt47gQfKZDShFN94TZs+afPW6BGUkecdytqGlX3YPTr7mom".
"spN0YAAAAASUVORK5CYII=";
$icons["h"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RV".
"h0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAHtSURBVDjLjZNLS9xQFMe138C9A/0OynyBUjeFQjdu".
"ROi2MMtCEalS0ToLEdQMdEShoKDWRymKigWxII7PhaB9aBFUJjHJpHlnnvbfe27NJcVIDfwIyT3nd885cOoA1B".
"HsaWQ0MZL/4SHjgciLCJpKpZJVrVava7Ua4mDnkCRpKCqJCpKU7HkefN8X2LYN0zShqiqXKIqCTCYjJGFyPQko".
"oFgsolwu8zfhui4sy4KmaVwQBAHokmw2+1cSClpSUmr12MP7LQunii8klOA4DnRdv9USn0koePRiJDW+aTGBjc".
"OLgAewlnjfYSuFQoFXIsvybQF9jG2avIKFPQtzOyZmcyZMtywkVAnNwzCMeMG7jV+YyFmQ1g30L2kYWitAWtZF".
"JdQOzYREsYLhzwZGGF+OHez/9PD2k4aeeYUHVyoVPheSELGCwRUdA+zG/VMPeycu3iyo6J5WxDxIQFA1QtCauU".
"wPrOpIPh/vSC+qSC/qPHn3u4uu2Su8nsrzZKqAoOR/BO2j+Q+DTPC0/2CdSu79qOLVlIyXk3l0zsjomJYxv6EL".
"QYgQPOk7a2jpOnmcaG57tvuD3fzNxc5XB9sEm0XuyMb5VcCriBI7A/bz9117EMO1ENxImtmAfDq4TzKLdfn2Rg".
"QJktxjnUNo9RN/AFmTwlP7TY1uAAAAAElFTkSuQmCC";
$icons["cpp"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAH/SURBVDjLjZPNaxNRFMWrf4cFwV13JVKXLuta61".
"apIChIV0rblUqhjYpRcUaNboxIqxFTQgVti4hQQTe1C7FFSUmnmvmM85XJzCSpx3efzmTSRtqBw7yZ9+5v7rl3".
"bg+AHhK7DjClmAZ20UGm/XFcApAKgsBqNptbrVYL3cT2IQjCnSQkCRig4FqtBs/zYtm2DdM0oaoqh8iyDFEUY0".
"gUvI8AdMD3fYRhyO8k13VhWRY0TeOAer0O+kg2m/0LIcDx9LdDgxff5jJzKjJzCmbe6fi0anEABTiOA13Xd1ji".
"NTlxfT01UVB/CfMG7r/WILxScaOo4FpeBrPEfUdWDMPgmVQqlTbgtCjls4sGjl16PxuRny5oGH3yA7oZoPjR4B".
"DbqeHlksLrUa1W24DJWRU3Wer9Qw/Gk+kVmA2lGuDKtMQzsVwfl6c3eE3IUgyYeCFjsqCgb3DqQhJwq/gTY7ly".
"V61Jdhtw7qFUSjNA/8m8kASkc5tYXnN4BvTs1kO23uAdIksx4OjI19Grzys4c7fkfCm5MO0QU483cf5eGcurNq".
"8BWfD8kK11HtwBoDYeGV4ZO5X57ow8knBWLGP49jqevVF5IKnRaOxQByD6kT6smFj6bHb0OoJsV1cAe/n7f3PQ".
"RVsx4B/kMCuQRxt7CWZnXT69CUAvQfYwzpFo9Hv/AD332dKni9XnAAAAAElFTkSuQmCC";
$icons["xls"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIpSURBVDjLjZNPSFRRFMZ/9707o0SOOshM0x/JFt".
"UmisKBooVEEUThsgi3KS0CN0G2lagWEYkSUdsRWgSFG9sVFAW1EIwQqRZiiDOZY804b967954249hUpB98y/Pj".
"O5zzKREBQCm1E0gDPv9XHpgTEQeAiFCDHAmCoBhFkTXGyL8cBIGMjo7eA3YDnog0ALJRFNlSqSTlcrnulZUVWV".
"5elsXFRTHGyMLCgoyNjdUhanCyV9ayOSeIdTgnOCtY43DWYY3j9ulxkskkYRjinCOXy40MDAzcZXCyVzZS38Me".
"KRQKf60EZPXSXInL9y+wLZMkCMs0RR28mJ2grSWJEo+lH9/IpNPE43GKxSLOOYwxpFIpAPTWjiaOtZ+gLdFKlJ".
"lD8u00xWP8lO/M5+e5efEB18b70VqjlMJai++vH8qLqoa+nn4+fJmiNNPCvMzQnIjzZuo1V88Ns3/HAcKKwfd9".
"tNZorYnFYuuAMLDMfJ3m+fQznr7L0Vk9zGpLmezB4zx++YggqhAFEZ7n4ft+HVQHVMoB5++cJNWaRrQwMjHM9q".
"CLTFcnJJq59WSIMLAopQDwfR/P8+oAbaqWK2eGSGxpxVrDnvQ+3s++4tPnj4SewYscUdUgIiilcM41/uXZG9kN".
"z9h9aa+EYdjg+hnDwHDq+iGsaXwcZ6XhsdZW+FOqFk0B3caYt4Bic3Ja66NerVACOGttBXCbGbbWrgJW/VbnXb".
"U6e5tMYIH8L54Xq0cq018+AAAAAElFTkSuQmCC";
$icons["fla"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAHYSURBVDjLjZPLSxtRFMa1f0UXCl0VN66igg80kQ".
"ZtsLiUWhe14MKFIFHbIEF8BNFFKYVkkT9GKFJooXTToq2gLkQT82oyjzuvO8nXe65mmIkRHfg2c+/3O+d8l9MB".
"oIMkvi6hkNDAA3om9MTz+QAhy7JqnPO667poJ3GOdDr92Q/xAwbIrOs6GGOeFEVBtVpFoVCQkHw+j0wm40Ga5k".
"4C0AXTNGHbNsxv32Hu7YNtp1Cr1VAsFiXAMAxQkWw2ewNpBZDZPjiA+XYebioJ9nIKqqqiVCrdGUlm0gpwzs5h".
"zrwGX1uGMTMLtvrBG6VcLstOcrncPQDOYW3tgCffw0isg4uqnP6J8AhCnVAelUqlPYD/PYE59wZ67BXsL4fg/6".
"ryYhNC82uaJkFtAdbHT+CJFbgbCagjYbDNlDev4zgyH4KQ7gA2n/fMUWWeiAtzBMrgWABAXciAhaibAKAYnXya".
"Gx3/5cSXoIajsH/8hHP8B87llTSSqAMSmQMAfSL2VYtET5WRCLcW3oHt7Aaq+s1+eQAt/EJXh8MNe2kRSmwa/L".
"oQeOsmpFUeQB0ag9I/jIve0G/n6Lhx3x60Ud3L4DbIPhEQo4PHmMVdTW6vD9BNkEesc1O0+t3/AXamvvzW7S+U".
"AAAAAElFTkSuQmCC";
$icons["ruby"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl".
"0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIESURBVDjLjZNPTxNBGIexid9CEr8DBr8CHEiMV".
"oomJiQkxBIM3dgIiaIESJTGGpVtyXIzHhoM4SIe9KAnEi4clQtJEczWFrbdP93d7s7u/JwZ7XYJBdnkyRxmfs/".
"MvO9OD4AeDvuuMPoY/f/hKiMR5WKCvlarpRNCwiAI0A02D1mW38QlcUE/DzebTdi2HWEYBhqNBqrVqpBUKhUUC".
"oVI0g5f4gK+wHVdeJ4nRo5lWdB1HbVaTQgcxwHfRFGUvxIuCKYfzmqZyZ2wKIO8fQ3/1Uv4Sy/QWliAO/sU9qM".
"ZmFMS3HfvT1xJ1ITOZJ9RpQi6+RH0y2fQb19BP23CVhRo+TysXA71+XkcMIk6fAfHK6tQVfWEoESXngNra0C5D".
"HZJYGMDZiaD35IEi41qOo3vc3MoJ1Ooj92HpmkdQZiVEsHUAzl88hjY3gYIAdbXYQ0MoDo4CH1kBHssvH8jCf3".
"eGKzDXzBNsyNoF/HH7WSJZLPA7i6wtQVnaAhmKoXjxUX8vDkMY3Qcnm6IInJOCS4nEte9QhF+RhInIRMTcFhYv".
"ZWCcXcUPmsl7w6H/w+nBFEb5SLc8TTo8jLq7M4m25mHfd8X8PC5AtHrXB5NdmwRrnfCcc4VCEnpA8jREasp6cp".
"ZAnrWO+hCGAn+Sa6xAtl84iJhttYSrzcm6OWSCzznNvzp9/4BgwKvG3Zq1eoAAAAASUVORK5CYII=";
$icons["exe"]="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0".
"RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFiSURBVBgZpcEhbpRRGIXh99x7IU0asGBJWEIdCL".
"aAqcFiCArFCkjA0KRJF0EF26kkFbVVdEj6/985zJ0wBjfp8ygJD6G3n358fP3m5NvtJscJYBObchEHx6QKJ6SK".
"snn6eLm7urr5/PP76cU4eXVy/ujouD074hDHd5s6By7GZknb3P7mUH+WNLZGKnx595JDvf96zTQSM92vRYA4lM".
"EEO5RNraHWUDH3FV48f0K5mAYJk5pQQpqIgixaE1JDKtRDd2OsYfJaTKNcTA2IBIIesMAOPdDUGYJSqGYml5lG".
"HHYkSGhAJBBIkAoWREAT3Z3JLqZhF3uS2EloQCQ8xLBxoAEWO7aZxros7EgISIIkwlZCY6s1OlAJTWFal5VppM".
"zUgbAlQcIkiT0DXSI2U2ymYZs9AWJL4n+df3pncsI0bn5dX344W05dhctUFbapZcE2ToiLVHBMbGymS7aUhIdo".
"PNBf7Jjw/gQ77u4AAAAASUVORK5CYII=";

$icon = $_GET["icon"];

if( in_array( $icon, array( "pkg", "bz2", "gz", "tgz", "tar", "tb2", "rar" ) ) ) $icon="zip"; // archives
if( in_array( $icon, array( "ppn", "jpg", "png", "gif", "bmp", "jpeg", "tga", "tiff" ) ) ) $icon="pic"; // images
if( in_array( $icon, array( "php4", "php5" ) ) ) $icon="php";
if( in_array( $icon, array( "rhtml", "rbx", "rb" ) ) ) $icon="ruby";
if( in_array( $icon, array( "swf", "flv" ) ) ) $icon="fla"; // flash
if( $icon=="html" ) $icon="htm";

$icon = array_key_exists( $icon, $icons ) ? $icon : "default"; // if we have registeret icon for a file ext, else default

echo base64_decode( $icons[ $icon ] );
die(); // Die, Jedi die!
} // END if( isSet( $_GET["icon"] ) )

$path = getcwd();
$script_name = getenv("SCRIPT_NAME");
if( $_GET["dir"]=="" && isSet( $_GET["dir"] ) ) header( "Location: ".$script_name );

define( "INDEXER_NAME", pathinfo( $_SERVER["PHP_SELF"], PATHINFO_BASENAME ) ); // nazwa tego pliku

if( strpos( $_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI], INDEXER_NAME ) == true ) // wywo�ano via http://server.com/dir/indexer.php
{
	$tmp = explode( INDEXER_NAME, $_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI] );
	/**
	* String that shows current
	*/
	define( "BASE_URL", $tmp[0] );
	unset( $tmp );
}
else // wywo�any via http://server.com/dir/
{
	define( "BASE_URL", $_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI] );
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>yaIndexer - <?php echo BASE_URL.$_GET["dir"]; ?></title>
	<meta name="Author" content="Daniel alkemic Czuba">
	<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-1"> 
	<style type="text/css">
	html, body
			{ background: #fff; font: 15px Verdana, Arial, Helvetica; }
	table	{ border-collapse: collapse; width: 65%; margin-left: auto; margin-right: auto;  }
	td		{ border: 0px solid #000; padding-left: 4px; padding-right: 4px; }
	a		{ text-decoration: none; }
	.wfile	{ border-bottom: 1px dashed #ddd; background: #fff; }
	.footer_note
			{ font-size: 8px; text-align: center;}
	.icon	{ width: 16px; height: 16px; margin-right: 4px; border: 0px; }
	.summary{ border-bottom: 1px dashed #444; background: #aaa; text-align: center; font-size: 12px; }
	</style>
</head>
<body>
<?php

/**
* return rounded up size + prefix
* @param integer
* @return string
*/
function roundSize( $value )
{
	$i=0;
	$names = array( "b", "kb", "mb", "gb", "tb", "eb" );
	if( $value >= 1024 ){
		while( $value >= 1024 ){
			$value = round( $value/1024, 2 );
			++$i;
		}
		return $value." ".$names[$i];
	}else{ return $value." ".$names[$i]; }
}

$dir = (!isSet($_GET["dir"]) ) ? "./" : urldecode( $_GET["dir"] )."/"; // je�li nie przeszli�my do jakiego� katalogu to wybieramy bierz�cy

if( !file_exists( $dir ) ) { echo "<h2>Path not found!</h2>"; exit; }
if( strstr( $dir, ".." ) ) { echo "<h2>Invalid path!</h2>"; exit; }

if( $dir!="" ){
	$path=split("/", $dir);
	if( $path[ (count( $path )-1) ] == "" ) unset($path[ (count( $path )-1) ]);
}
/**
 * Return an array with information about $dir
 * @param name of dir relative
 * @return array
 */
function generateDirContent( $dir )
{

	if( $dir=="" ) $dir="./";
	$current_dir = dir( $dir );
	$i=0; $j=0;

	if( SHOW_SUMMARY ) $content["summary"]["size"]=0;

	while ( $current_file = $current_dir->read() ) // pobiera zawarto�� katalogu
	{
		if( !( HIDE_HIDDEN AND $current_file[0]==="." ) )
		{
			if( is_file( $dir.$current_file ) ) // listing plik�w
			{
				$file_stat = stat($dir.$current_file);

				$content["files"][$i]["name"]  = $current_file;
				$content["files"][$i]["size"]  = roundSize( $file_stat["size"] );
				$content["files"][$i]["mtime"] = date("d-m-Y G:i:s" ,$file_stat["mtime"]);

				if( SHOW_SUMMARY ) {
					$content["summary"]["file_count"]++;
					$content["summary"]["size"]=$content["summary"]["size"]+$file_stat["size"];
				}
				$i++;
		
			} // if( is_file( $dir.$current_file ) ) 
			elseif( $current_file!='.' && $current_file!='..' && is_dir($dir.$current_file) ){ // listing katalog�w
				$dir_stat = stat($dir.$current_file);
		
				$content["dirs"][$j]["name"]  = $current_file;
				$content["dirs"][$j]["size"]  = "-";
				$content["dirs"][$j]["mtime"] = date("d-m-Y h:i:s" ,$dir_stat["mtime"]);

				if( SHOW_SUMMARY ) $content["summary"]["dir_count"]=$content["summary"]["dir_count"]+1;
				$j++;
			} // END elseif( $current_file!='.' && $current_file!='..' && is_dir($dir.$current_file) )
		}// END if( !SHOW_HIDDEN || $current_file[0]==="." )	
	} // END while ( $current_file = $current_dir->read() ) 
	$current_dir->close();

	@sort( $content["files"] );
	@sort( $content["dirs"] );

	return $content;
}

/**
 * Array caontains data about curent directory.
 * @global array $content
 */ 
$content = generateDirContent( $dir );
/**
 * How many we have files.
 * @global integer $count_files
 */ 
$count_files = count( $content["files"] );
/**
 * How many we have directories.
 * @global integer $count_dirs
 */ 
$count_dirs = count( $content["dirs"] );

if( $path[0]!="." )
{
	$depth = count( $path );
	$tmp_path=""; $tmp="";
	for( $i=0; $i<$depth; $i++ )
	{
		$i==0 ? $tmp_path .= $path[$i] : $tmp_path .= "/".$path[$i];
		$tmp.=" &raquo; <a href=\"http://".BASE_URL.INDEXER_NAME."?dir=".urlencode($tmp_path)."\"> $path[$i]</a> ";
	}
}
else
{
	$tmp="";
}

echo "
<table cellpadding=\"1\">
<tr><td colspan=\"3\">Path: <a href=\"http://".BASE_URL."\">Home</a>".$tmp."</td></tr>
<tr><td style=\"width: 55%;\">Name</td><td style=\"width: 15%;\">Size</td><td style=\"width: 30%;\">Time</td></tr>\n";

if( $dir!="" && $dir!="./" )
{
	echo 
"<tr class=\"wfile\">
	<td><a href=\"".$script_name."?dir=";
	for ( $a=0; $a<(count( $path )-1); $a++ ){ echo urlencode( str_replace( "./", "", str_replace( "//", "/", $path[$a]."/") ) ); }
	echo "\"><img class=\"icon\" src=\"?icon=up\" alt=\"icon\">parent directory</a></td>
	<td>-</td>
	<td>-</td>
</tr>";
}

for( $i=0; $i<$count_dirs; $i++ )
{
	$c_dir = urlencode( str_replace( "./", "", str_replace( "//", "/", $dir.$content["dirs"][$i]["name"]) ) );
	echo "
<tr class=\"wfile\">
	<td><a href=\"".$script_name."?dir=".$c_dir."\"><img class=\"icon\" src=\"?icon=dir\" alt=\"icon\">". $content["dirs"][$i]["name"]."/</a></td>
	<td>".$content["dirs"][$i]["size"]."</td>
	<td>".$content["dirs"][$i]["mtime"]."</td>
</tr>";
}

for( $i=0; $i<$count_files; $i++ ){
	echo "
<tr class=\"wfile\">
	<td><a href=\"".$dir.$content["files"][$i]["name"]."\"><img class=\"icon\" src=\"?icon=".
	pathinfo( $content["files"][$i]["name"], PATHINFO_EXTENSION )."\" alt=\"icon\">".$content["files"][$i]["name"]."</a></td>
	<td>".$content["files"][$i]["size"]."</td>
	<td>".$content["files"][$i]["mtime"]."</td>
</tr>";
}
if( SHOW_SUMMARY )// pokazujemy skr�con� statystyk� katalogu
	echo "<tr class=\"summary\"><td colspan=\"3\">There is ".$count_files.
	" dir(s) and ".$count_dirs." file(s) of total size: ". roundSize( $content["summary"]["size"] );

?>
<tr><td colspan="3" class="footer_note">
<br>
<b>yaIndexer</b> v.<?php echo YAI_VERSION; ?> &copy; by 
<a title="alkemic" href="http://alkemic.co.cc/"><b>alkemic</b></a></td></tr>
</table>
</body>
</html>
