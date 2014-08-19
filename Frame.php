<?php

/* Пространство имен */
namespace AMpay;

class Frame {
    private  
            /* Параметры запроса фрэйма */
            
            /* тип платежа */
            $payType,
            /* уникальный токен, который присваивается ТСП для работы с фреймом 
            «mk» - оплата с мобильного телефона (мобильная коммерция), 
            «card» - оплата с пластиковой карты (интернет эквайринг).  */       
            $token,          
           /* номер получателя платежа (номер договора, номер заказа, номер объявления, лицевой счёт в системе получателя и т.д.) */
            $receiverNum = '',
            /* сумма платежа в диапазоне от 10 до 14999 рублей */
            $payAmount = '',
            /* номер телефона в 10-ти значном формате (например, 9001234455) */
            $telNum= '',
            /*адрес (URL) CSS файла*/
            $cssUrl = '',
            /* режим работы платежной страницы ("1" - отображать во фрейме, иначе на всю страницу), по умолчанию "1" */
            $frameOpt = '1',
            
            /* frameParams */
            $border =  '0',// по умолчанию frameborder='0' 
            $width = '750',//по умолчанию width='750' 
            $height = '750',//по умолчанию height='750' 
            $scroll = 'auto',//по умолчанию scrolling='auto'
            
            /* url-адрес единого фрэйма */
            $frameUrl = 'https://arsenalpay.ru/payframe/pay.php';
    
    /*
     * В параметрах конструктора инициализируются тип платежа и уникальный токен (подробно в ReadMe)
     */
    public function __construct( $payType, $token )
    {
        $this->payType = $payType;
        $this->token = $token; 
    }
    
    /*
     * Функция установки параметров отображения фрэйма
     */
    public function setView( $fwidth, $fheight, $fborder, $fscroll ){
        $this->width = $fwidth;
        $this->height = $fheight;
        $this->border = $fborder;
        $this->scroll = $fscroll;       
    }
    
    /*
     * Функция вызова фрэйма 
     */
    public function getFrame(){
    
        $this->cssUrl = filter_input( INPUT_GET,'css' );
        $this->receiverNum = filter_input( INPUT_GET,'n' );
        $this->payAmount = filter_input( INPUT_GET,'a' );
        $this->telNum = filter_input( INPUT_GET,'msisdn' );
        
        $frameParams = "frameborder={$this->border} width={$this->width} scrolling={$this->scroll} height={$this->height}";

        $frame = "<p><iframe src=".$this->frameUrl."?src=".$this->payType."&t=".$this ->token."&n=".$this->receiverNum.
                "&a=".$this->payAmount."&msisdn=".$this->telNum."&frame=".$this->frameOpt." ".$frameParams.">"
                . "</iframe></p>";
        return $frame;
    }    
}
    









