<?php
/**
 * ----------------------------------------------------------------------
 *  
 * Copyright (c) 2006-2012 Khaled Al-Sham'aa.
 *  
 * http://www.ar-php.org
 *  
 * PHP Version 5 
 *  
 * ----------------------------------------------------------------------
 *  
 * LICENSE
 *
 * This program is open source product; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License (LGPL)
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *  
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 *  
 * ----------------------------------------------------------------------
 *  
 * Class Name: Spell numbers in the Arabic idiom
 *  
 * Filename:   Numbers.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled@ar-php.org>
 *  
 * Purpose:    Spell numbers in the Arabic idiom
 *  
 * ----------------------------------------------------------------------
 *  
 * Spell numbers in the Arabic idiom
 *
 * PHP class to spell numbers in the Arabic idiom. This function is very
 * useful for financial applications in Arabic for example.
 *    
 * If you ever have to create an Arabic PHP application built around invoicing or 
 * accounting, you might find this class useful. Its sole reason for existence is 
 * to help you translate integers into their spoken-word equivalents in Arabic 
 * language.
 * 
 * How is this useful? Well, consider the typical invoice: In addition to a 
 * description of the work done, the date, and the hourly or project cost, it always 
 * includes a total cost at the end, the amount that the customer is expected 
 * to pay.
 *   
 * To avoid any misinterpretation of the total amount, many organizations (mine 
 * included) put the amount in both words and figures; for example, $1,200 becomes 
 * "one thousand and two hundred dollars." You probably do the same thing every time 
 * you write a check.
 * 
 * Now take this scenario to a Web-based invoicing system. The actual data used to 
 * generate the invoice will be stored in a database as integers, both to save space 
 * and to simplify calculations. So when a printable invoice is generated, your Web 
 * application will need to convert those integers into words, this is more clarity 
 * and more personality.
 * 
 * This class will accept almost any numeric value and convert it into an equivalent 
 * string of words in written Arabic language (using Windows-1256 character set). 
 * The value can be any positive number up to 999,999,999 (users should not use 
 * commas). It will take care of feminine and Arabic grammar rules.
 *
 * Example:
 * <code>
 *     include('./I18N/Arabic.php');
 *     $obj = new I18N_Arabic('Numbers');
 *     
 *     $obj->setFeminine(1);
 *     $obj->setFormat(1);
 *     
 *     $integer = 2147483647;
 *     
 *     $text = $obj->int2str($integer);
 *     
 *     echo "<p align=\"right\"><b class=hilight>$integer</b><br />$text</p>";
 * 
 *     $obj->setFeminine(2);
 *     $obj->setFormat(2);
 *     
 *     $integer = 2147483647;
 *     
 *     $text = $obj->int2str($integer);
 *     
 *     echo "<p align=\"right\"><b class=hilight>$integer</b><br />$text</p>";   
 * </code>
 *             
 * @category  I18N 
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2012 Khaled Al-Sham'aa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */

// New in PHP V5.3: Namespaces
// namespace I18N\Arabic;
// 
// $obj = new I18N\Arabic\Numbers();
// 
// use I18N\Arabic;
// $obj = new Arabic\Numbers();
//
// use I18N\Arabic\Numbers as Numbers;
// $obj = new Numbers();

/**
 * This PHP class spell numbers in the Arabic idiom
 *  
 * @category  I18N 
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2012 Khaled Al-Sham'aa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class I18N_Arabic_Numbers
{
    private $_individual    = array();
    private $_complications = array();
    private $_arabicIndic   = array();
    private $_feminine      = 1;
    private $_format        = 1;

    /**
     * Loads initialize values
     *
     * @ignore
     */         
    public function __construct()
    {
        $xml = simplexml_load_file(dirname(__FILE__).'/data/ArNumbers.xml');

        foreach ($xml->xpath("//individual/number[@gender='male']") as $num) {
            if (isset($num['grammar'])) {
                $grammar = $num['grammar'];
                
                $this->_individual["{$num['value']}"][1]["$grammar"] = (string)$num;
            } else {
                $this->_individual["{$num['value']}"][1] = (string)$num;
            }
        } 
        
        foreach ($xml->xpath("//individual/number[@gender='female']") as $num) {
            if (isset($num['grammar'])) {
                $grammar = $num['grammar'];
                
                $this->_individual["{$num['value']}"][2]["$grammar"] = (string)$num;
            } else {
                $this->_individual["{$num['value']}"][2] = (string)$num;
            }
        } 
        
        foreach ($xml->xpath("//individual/number[@value>19]") as $num) {
            if (isset($num['grammar'])) {
                $grammar = $num['grammar'];
                
                $this->_individual["{$num['value']}"]["$grammar"] = (string)$num;
            } else {
                $this->_individual["{$num['value']}"] = (string)$num;
            }
        } 
        
        foreach ($xml->complications->number as $num) {
            $scale  = $num['scale'];
            $format = $num['format'];
            
            $this->_complications["$scale"]["$format"] = (string)$num;
        } 
        
        foreach ($xml->arabicIndic->number as $html) {
            $value  = $html['value'];
            
            $this->_arabicIndic["$value"] = $html;
        } 
    }
    
    /**
     * Set feminine flag of the counted object
     *      
     * @param integer $value Counted object feminine 
     *                      (1 for masculine & 2 for feminine)
     *      
     * @return object $this to build a fluent interface
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function setFeminine($value)
    {
        if ($value == 1 || $value == 2) {
            $this->_feminine = $value;
        }
        
        return $this;
    }
    
    /**
     * Set the grammar position flag of the counted object
     *      
     * @param integer $value Grammar position of counted object
     *                       (1 if Marfoua & 2 if Mansoub or Majrour)
     *                            
     * @return object $this to build a fluent interface
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function setFormat($value)
    {
        if ($value == 1 || $value == 2) {
            $this->_format = $value;
        }
        
        return $this;
    }
    
    /**
     * Get the feminine flag of counted object
     *      
     * @return integer return current setting of counted object feminine flag
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function getFeminine()
    {
        return $this->_feminine;
    }
    
    /**
     * Get the grammer position flag of counted object
     *      
     * @return integer return current setting of counted object grammer
     *                 position flag
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function getFormat()
    {
        return $this->_format;
    }
    
    /**
     * Spell integer number in Arabic idiom
     *      
     * @param integer $number The number you want to spell in Arabic idiom
     *                    
     * @return string The Arabic idiom that spells inserted number
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function int2str($number)
    {
        if ($number < 0) {
            $string = 'سالب ';
            $number = (string) -1 * $number;
        } else {
            $string = '';
        }
        
        $temp = explode('.', $number);

        $string .= $this->subInt2str($temp[0]);

        if (!empty($temp[1])) {
            $dec     = $this->subInt2str($temp[1]);
            $string .= ' فاصلة ' . $dec; 
        }
        
        return $string;
    }
    
    /**
     * Spell integer number in Arabic idiom
     *      
     * @param integer $number The number you want to spell in Arabic idiom
     * @param logical $zero   Present leading zero if true [default is true]     
     *      
     * @return string The Arabic idiom that spells inserted number
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    protected function subInt2str($number, $zero = true)
    {
        $blocks = array();
        $items  = array();
        $zeros  = '';
        $string = '';
        $number = ($zero != false) ? trim($number) : trim((float)$number);
        
        if ($number > 0) {
        
            //--- by Jnom: handle left zero
            // http://www.itkane.com
            // jnom23@gmail.com
            if ($zero != false) {
                $fulnum = $number;
                while (($fulnum[0]) == '0') {
                    $zeros = 'صفر '.$zeros;
                    $fulnum = substr($fulnum, 1, strlen($fulnum));
                };
            };
            //---/

            while (strlen($number) > 3) {
                array_push($blocks, substr($number, -3));
                $number = substr($number, 0, strlen($number) - 3);
            }
            array_push($blocks, $number);
            
            $blocks_num = count($blocks) - 1;
  
            for ($i = $blocks_num; $i >= 0; $i--) {
                $number = floor($blocks[$i]);
  
                $text = $this->writtenBlock($number);
                if ($text) {
                    if ($number == 1 && $i != 0) {
                        $text = $this->_complications[$i][4];
                    } elseif ($number == 2 && $i != 0) {
                        $text = $this->_complications[$i][$this->_format];
                    } elseif ($number > 2 && $number < 11 && $i != 0) {
                        $text .= ' ' . $this->_complications[$i][3];
                    } elseif ($i != 0) {
                        $text .= ' ' . $this->_complications[$i][4];
                    }
                    
                    //--- by Jnom: handle left zero
                    if ($text != '' && $zeros != '' && $zero != false) {
                        $text  = $zeros.' '.$text;
                        $zeros = '';
                    };
                    //---/

                    array_push($items, $text);
                }
            }
            
            $string = implode(' و ', $items);
        } else {
            $string = 'صفر';
        }
        return $string;
    }
    
    /**
     * Spell sub block number of three digits max in Arabic idiom
     *      
     * @param integer $number Sub block number of three digits max you want to 
     *                        spell in Arabic idiom
     *                      
     * @return string The Arabic idiom that spells inserted sub block
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    protected function writtenBlock($number)
    {
        $items  = array();
        $string = '';
        
        if ($number > 99) {
            $hundred = floor($number / 100) * 100;
            $number  = $number % 100;
            
            if ($hundred == 200) {
                array_push($items, $this->_individual[$hundred][$this->_format]);
            } else {
                array_push($items, $this->_individual[$hundred]);
            }
        }
        
        if ($number != 0) {
            if ($number == 2 || $number == 12) {
                array_push($items, $this->_individual[$number]
                                                    [$this->_feminine]
                                                    [$this->_format]);
            } elseif ($number < 20) {
                array_push($items, $this->_individual[$number][$this->_feminine]);
            } else {
                $ones = $number % 10;
                $tens = floor($number / 10) * 10;
                
                if ($ones == 2) {
                    array_push($items, $this->_individual[$ones]
                                                        [$this->_feminine]
                                                        [$this->_format]);
                } elseif ($ones > 0) {
                    array_push($items, $this->_individual[$ones][$this->_feminine]);
                }
                
                array_push($items, $this->_individual[$tens][$this->_format]);
            }
        }
        
        $items = array_diff($items, array(''));
        
        $string = implode(' و ', $items);
        
        return $string;
    }

    
    /**
     * Represent integer number in Arabic-Indic digits using HTML entities
     *      
     * @param integer $number The number you want to present in Arabic-Indic digits
     *                        using HTML entities
     *                    
     * @return string The Arabic-Indic digits represent inserted integer number using 
     *                HTML entities
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function int2indic($number)
    {
        $str = strtr("$number", $this->_arabicIndic);

        return $str;
    }
}
