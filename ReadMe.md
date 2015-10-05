PHP ArsenalPay API SDK
====================

*Arsenal Media LLC*
[ArsenalPay processing server][1]

PHP ArsenalPay API SDK contains methods to make integration of your php application with processing server of ArsenalPay  easier and faster .

Version
---------
1.0

Source
---------
[Official integration guide page][2]

Manual  Installation
------------------------
To obtain the latest version  of PHP ArsenalPay Api SDK use:
`git clone https://github.com/php-arsenalmedia-api-sdk`
To  have access to SDK methods add the following to your php script:
`require_once("/path/to/php-arsenalpay-api-sdk/lib");`

Methods of API
-------------------
Class `Frame` in `Frame.php`:
*In constructor parameters of class `Frame`  indicate:*

- *payment type (`mk` - payment from mobile phone or `card` - payment from plastic card);*
 
- *your unique token received from Arsenal pay.*
`$frame = new AMpay\Frame( payment type, 'your unique token' );`

`getFrame()` - main method to return the ready frame for making payments of ArsenalPay. 
`setView( $fwidth, $fheight, $fborder, $fscroll )` - additional method to set the view parameters of the frame, such as height, width, existence of border and scrolling.

Class `CallBack` in `CallBack.php`:
`handleCallBack('keyword to check the sign')` - method to receive, check and acknowledge receipt of sent data from Arsenal Pay processing server about payment or recipient of payment. 

####Data handled by handleCallBack(...) method
GET or POST method  of HTTP protocol is used for data transfer about payment or payment recipient which is formatted as query with the following parameters:
`ID=xxx&FUNCTION=check&RRN=xxx&PAYER=xxx&AMOUNT=xxx&ACCOUNT=xxx&STATUS=check&DATETIME=xxx&SIGN=xxx`
`SIGN` is obtained by implementing the hash function to concatenation of every query  parameter in consecutive way with a keyword `PASSWORD` :
`SIGN = md5(md5(ID).md(FUNCTION).md5(RRN).md5(PAYER).md5(AMOUNT).md5(ACCOUNT).md(STATUS).md5(PASSWORD))`

**Other parameters of the query:**
`ID` - identification of your system
`FUNCTION` - query type (`payment` - query about payment, `check` - query about payment receiver )
`RRN` - transaction identification
`PAYER` - payer identification
`AMOUNT` - payment amount
`ACCOUNT` - payment receiver number
`STATUS` - payment status
`DATETIME` - URL-coded date and time in format ISO-8601 (YYYY-MM-DDThh:mm:ss±hh:mm)
`SIGN` - query sign


Answer codes on the query about payment:
`OK` - Notification is successfully received by your system
`ERR` - Error of notification receipt by your system

Answer codes on the query about payment recipient:
`YES` - Payment recipient exists in your system
`NO` - Payment recipient does not exist in your system
`ERR` - Error in checking of payment recipient in your system


Usage of methods
-------------------
```php
require_once 'Frame.php';
$frame = new AMpay\Frame( 'mk', 'your unique token' );
$frame->setView( '750', '750', '0', 'auto' );
echo $frame->getFrame();
```
```php
require_once 'CallBack.php';
$callBack = new AMpay\CallBack;
$callBack->handleCallBack('keyword to check the sign');
```

GET parameters of Frame
=====================
It is possible to transfer some parameters by GET method of HTTP protocol to the frame of Arsenal Media Pay:
`yourScriptWithCallOfFrameClassMethods?n=&а=&msisdn=&s=&css=&frame=` 

`n` - payment recipient number (contract number, number of the order, etc.)
`a` - payment amount in  limit of 10 to 14999 RUB
`msisdn` - mobile number in 10-digit format (*example: 9001234455*) if payment type is  from mobile number (`mk`).
`css` - address (URL) of CSS file
`frame` - mode option of frame view ("1" - full screen mode of frame view )

[1]: https://arsenalpay.ru/
[2]: https://arsenalpay.ru/documentation.html
