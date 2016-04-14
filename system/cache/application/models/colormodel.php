<?php  
class Colormodel extends model
    {
	
        private $hexColor = array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
        private $newColor = "";
        private $colorBag = array();

        function getColor()
        {
            $this->newColor="#".$this->hexColor[$this->genRandom()].
                                $this->hexColor[$this->genRandom()].
                                $this->hexColor[$this->genRandom()].
                                $this->hexColor[$this->genRandom()].
                                $this->hexColor[$this->genRandom()].
                                $this->hexColor[$this->genRandom()];
                                
            if(!in_array($this->newColor,$this->colorBag))
            {
                $this->colorBag[] = $this->newColor;
                return $this->newColor;
            }
        }

        function genRandom()
        {
            srand((float) microtime() * 10000000);
            $random_col_keys = array_rand($this->hexColor, 2);
            //echo ' '.$random_col_keys[0];
            return $random_col_keys[0];
        }
    }
/* end of colormodel */
