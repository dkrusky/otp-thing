<?php
if(!defined('LIVE')) { exit(); };
/*
#
# QRcode/OTP class library for PHP5
#
# This version supports QRcode model2 version 1-40.
# Several functions are not supported.
#
# This class simplifies the creation of OTP qr codes and their keys
#
# The method GenerateQRCode() is based off code written in version 0.50beta14 (C)2002-2013,Y.Swetake , and has been
# modified to output base64 encoded image as well as load data from the included sqlite database.
*/

class otp {
	public static $company = 'ACME';
	public static $digits = 6;
	public static $period = 30;
	public static $totp = false;
	public static $algorithm = 'sha1';

	private static $secret;
	private static $user;

	public static $qrcode_database				= "lib/qrdata.db";

	static $qrcode_version						= 6;
	static $qrcode_errorcorrect					= "L";
	static $qrcode_structureappend_n			= 0;
	static $qrcode_structureappend_m			= 0;
	static $qrcode_structureappend_parity		= "";
	static $qrcode_structureappend_originaldata	= "";
	static $qrcode_module_size					= 5;
	static $qrcode_quiet_zone					= 2;


	// create a new secret [ random 16 valid base32 characters ]
	public static function GenerateSecret($user) {
		self::$user = $user;
		self::$secret = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', mt_rand(1,16))),1,16);
		return implode( '-', str_split(strtolower(self::$secret), 4) );
	}

	// set the current user and secret
	public static function SetSecret($user, $secret) {
		self::$user = $user;
		self::$secret = preg_replace("/[^A-Z2-7]+/", "", strtoupper($secret));
	}

	// gets the current time block
	public static function GetTime() {
		return floor(microtime(true)/self::$period);
	}

	// gets the current code for the current timeblock on the current secret
	public static function GetCode() {
		if(empty(self::$secret)) { throw new Exception('Missing required data'); }
		self::$algorithm = strtolower(self::$algorithm);
		if (!in_array(self::$algorithm, array('sha1', 'sha256', 'sha512'), true)) { throw new Exception('Invalid algorithm'); }
		$time = bcdiv(time() , self::$period, 0);
		if(self::$totp === true || self::$algorithm != 'sha1') {
			// TOTP
			$hash = hash_hmac(
						self::$algorithm,
						hex2bin(str_pad(base_convert($time, 10, 16),16,"0", STR_PAD_LEFT)),
						self::decode(),
						true
					);
			$offset = ord($hash[strlen($hash) - 1]) & 0xf;
		} else {
			// HOTP
			//$time = floor(microtime(true)/self::$period);
			$hash = hash_hmac(
						'sha1',
						pack('N*', 0) . pack('N*', $time),
						self::decode(),
						true
					);
			$offset = ord($hash[19]) & 0xf;
		}
		$OTP = (
			((ord($hash[$offset+0]) & 0x7f) << 24 ) |
			((ord($hash[$offset+1]) & 0xff) << 16 ) |
			((ord($hash[$offset+2]) & 0xff) << 8 ) |
			(ord($hash[$offset+3]) & 0xff)
		) % pow(10, self::$digits);

		return (object)Array(
			'timeblock' => $time,
			'code' => str_pad($OTP, self::$digits, '0', STR_PAD_LEFT)
		);
	}

	/* validate code */
	public static function ValidateCode($code, $hashtype=null) {
		// get valid code for current timeblock
		$vcode = self::GetCode();
		if($hashtype != null) {
			$hashtype = strtolower($hashtype);
			$vcode->code = hash($hashtype, $vcode->code);
		}

		if($code === $vcode->code) {
			return true;
			// recommended action on true is to use mark the current time
			// and disallow this code from being used again for a lockout
			// period of at least 1 day.
		} else {
			return false;
			// recommended action on false is to use an internal counter and
			// then wait for the next time block to try again
		}
	}

	// decode base32
	private static function decode() {
		if(empty(self::$secret)) { throw new Exception('Missing required data'); }
		$lut = array(
			"A" => 0,       "B" => 1,
			"C" => 2,       "D" => 3,
			"E" => 4,       "F" => 5,
			"G" => 6,       "H" => 7,
			"I" => 8,       "J" => 9,
			"K" => 10,      "L" => 11,
			"M" => 12,      "N" => 13,
			"O" => 14,      "P" => 15,
			"Q" => 16,      "R" => 17,
			"S" => 18,      "T" => 19,
			"U" => 20,      "V" => 21,
			"W" => 22,      "X" => 23,
			"Y" => 24,      "Z" => 25,
			"2" => 26,      "3" => 27,
			"4" => 28,      "5" => 29,
			"6" => 30,      "7" => 31
		);
		$b32    = strtoupper(self::$secret);
		$l      = strlen($b32);
		$n      = 0;
		$j      = 0;
		$binary = "";

		for ($i = 0; $i < $l; $i++) {
			$n = $n << 5;
			$n = $n + $lut[$b32[$i]];
			$j = $j + 5;
			if ($j >= 8) {
				$j = $j - 8;
				$binary .= chr(($n & (0xFF << $j)) >> $j);
			}
		}
		return $binary;
	}

	// create the qr code, and return an array containing the image, otpauth url, and current secret
	public static function GenerateQRCode() {
		if(empty(self::$secret) || empty(self::$user)) { throw new Exception('Missing required data'); }

		// Generate OPT url
		$qrcode_data_string = 	'otpauth://totp/' .
			rawurlencode(self::$company) . ':' .
			self::$user .
			'?secret=' . strtoupper(self::$secret) .
			'&issuer=' . rawurlencode(self::$company) .
			'&algorithm=' . strtoupper(self::$algorithm) .
			'&digits=' . self::$digits .
			'&period=' . self::$period
		;

		// Handle QR CODE stuff
		$data_length=strlen($qrcode_data_string);
		$data_counter=0;

		if (self::$qrcode_structureappend_n>1){
			$data_value[0]=3;
			$data_bits[0]=4;
			$data_value[1]=self::$qrcode_structureappend_m-1;
			$data_bits[1]=4;
			$data_value[2]=self::$qrcode_structureappend_n-1;
			$data_bits[2]=4;
			$data_value[3]=self::$qrcode_structureappend_parity;
			$data_bits[3]=8;
			$data_counter=4;
		}
		$data_bits[$data_counter]=4;

		/*  --- determine encode mode */
		if (preg_match("/[^0-9]/",$qrcode_data_string)!=0){
			if (preg_match("/[^0-9A-Z \$\*\%\+\.\/\:\-]/",$qrcode_data_string)!=0) {
				/*  --- 8bit byte mode */
				$codeword_num_plus=array(0,0,0,0,0,0,0,0,0,0,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8);
				$data_value[$data_counter]=4;
				$data_counter++;
				$data_value[$data_counter]=$data_length;
				$data_bits[$data_counter]=8;   /* #version 1-9 */
				$codeword_num_counter_value=$data_counter;
				$data_counter++;
				$i=0;
				while ($i<$data_length){
					$data_value[$data_counter]=ord(substr($qrcode_data_string,$i,1));
					$data_bits[$data_counter]=8;
					$data_counter++;
					$i++;
				}
			} else {
				/* ---- alphanumeric mode */
				$codeword_num_plus=array(0,0,0,0,0,0,0,0,0,0,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,4,4,4,4,4,4,4,4,4,4,4,4,4,4);
				$data_value[$data_counter]=2;
				$data_counter++;
				$data_value[$data_counter]=$data_length;
				$data_bits[$data_counter]=9;  /* #version 1-9 */
				$codeword_num_counter_value=$data_counter;
				$alphanumeric_character_hash=array("0"=>0,"1"=>1,"2"=>2,"3"=>3,"4"=>4,"5"=>5,"6"=>6,"7"=>7,"8"=>8,"9"=>9,"A"=>10,"B"=>11,"C"=>12,"D"=>13,"E"=>14,"F"=>15,"G"=>16,"H"=>17,"I"=>18,"J"=>19,"K"=>20,"L"=>21,"M"=>22,"N"=>23,"O"=>24,"P"=>25,"Q"=>26,"R"=>27,"S"=>28,"T"=>29,"U"=>30,"V"=>31,"W"=>32,"X"=>33,"Y"=>34,"Z"=>35," "=>36,"$"=>37,"%"=>38,"*"=>39,"+"=>40,"-"=>41,"."=>42,"/"=>43,":"=>44);
				$i=0;
				$data_counter++;
				while ($i<$data_length){
					if (($i %2)==0){
						$data_value[$data_counter]=$alphanumeric_character_hash[substr($qrcode_data_string,$i,1)];
						$data_bits[$data_counter]=6;
					} else {
						$data_value[$data_counter]=$data_value[$data_counter]*45+$alphanumeric_character_hash[substr($qrcode_data_string,$i,1)];
						$data_bits[$data_counter]=11;
						$data_counter++;
					}
					$i++;
				}
			}
		} else {
			/* ---- numeric mode */
			$codeword_num_plus=array(0,0,0,0,0,0,0,0,0,0,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,4,4,4,4,4,4,4,4,4,4,4,4,4,4);
			$data_value[$data_counter]=1;
			$data_counter++;
			$data_value[$data_counter]=$data_length;
			$data_bits[$data_counter]=10;   /* #version 1-9 */
			$codeword_num_counter_value=$data_counter;
			$i=0;
			$data_counter++;
			while ($i<$data_length){
				if (($i % 3)==0){
					$data_value[$data_counter]=substr($qrcode_data_string,$i,1);
					$data_bits[$data_counter]=4;
				} else {
					$data_value[$data_counter]=$data_value[$data_counter]*10+substr($qrcode_data_string,$i,1);
					if (($i % 3)==1){
						$data_bits[$data_counter]=7;
					} else {
						$data_bits[$data_counter]=10;
						$data_counter++;
					}
				}
				$i++;
			}
		}

		if (@$data_bits[$data_counter]>0) { $data_counter++; }
		$i=0;
		$total_data_bits=0;
		while($i<$data_counter){ $total_data_bits+=$data_bits[$i]; $i++; }
		$ecc_character_hash=array("L"=>"1","l"=>"1","M"=>"0","m"=>"0","Q"=>"3","q"=>"3","H"=>"2","h"=>"2");
		$ec=@$ecc_character_hash[self::$qrcode_errorcorrect]; 
		if (!$ec){$ec=0;}
		$max_data_bits_array=array(0,128,224,352,512,688,864,992,1232,1456,1728,2032,2320,2672,2920,3320,3624,4056,4504,5016,5352,5712,6256,6880,7312,8000,8496,9024,9544,10136,10984,11640,12328,13048,13800,14496,15312,15936,16816,17728,18672,152,272,440,640,864,1088,1248,1552,1856,2192,2592,2960,3424,3688,4184,4712,5176,5768,6360,6888,7456,8048,8752,9392,10208,10960,11744,12248,13048,13880,14744,15640,16568,17528,18448,19472,20528,21616,22496,23648,72,128,208,288,368,480,528,688,800,976,1120,1264,1440,1576,1784,2024,2264,2504,2728,3080,3248,3536,3712,4112,4304,4768,5024,5288,5608,5960,6344,6760,7208,7688,7888,8432,8768,9136,9776,10208,104,176,272,384,496,608,704,880,1056,1232,1440,1648,1952,2088,2360,2600,2936,3176,3560,3880,4096,4544,4912,5312,5744,6032,6464,6968,7288,7880,8264,8920,9368,9848,10288,10832,11408,12016,12656,13328 );
		if (!self::$qrcode_version){
			/* #--- auto version select */
			$i=1+40*$ec;
			$j=$i+39;
			self::$qrcode_version=1; 
			while ($i<=$j){
				if (($max_data_bits_array[$i])>=$total_data_bits+$codeword_num_plus[self::$qrcode_version]){
					$max_data_bits=$max_data_bits_array[$i];
					break;
				}
				$i++;
				self::$qrcode_version++;
			}
		} else {
			$max_data_bits=$max_data_bits_array[self::$qrcode_version+40*$ec];
		}
		$total_data_bits+=$codeword_num_plus[self::$qrcode_version];
		$data_bits[$codeword_num_counter_value]+=$codeword_num_plus[self::$qrcode_version];
		$max_codewords_array=array(0,26,44,70,100,134,172,196,242,292,346,404,466,532,581,655,733,815,901,991,1085,1156, 1258,1364,1474,1588,1706,1828,1921,2051,2185,2323,2465,2611,2761,2876,3034,3196,3362,3532,3706);
		$max_codewords=$max_codewords_array[self::$qrcode_version];
		$max_modules_1side=17+(self::$qrcode_version <<2);
		$matrix_remain_bit=array(0,0,7,7,7,7,7,0,0,0,0,0,0,0,3,3,3,3,3,3,3,4,4,4,4,4,4,4,3,3,3,3,3,3,3,0,0,0,0,0,0);

		/* ---- read version ECC data file */
		$byte_num=$matrix_remain_bit[self::$qrcode_version]+($max_codewords << 3);

		$qrcodedb = new qrcodedb(self::$qrcode_database);
		$qrv = $qrcodedb->query('SELECT Data FROM qr WHERE Name = \'qrv' . self::$qrcode_version."_".$ec . '\'');
		sread::open(gzdecode(hex2bin($qrv['Data'])));

		$matx = sread::read($byte_num);

		$maty = sread::read($byte_num);
		$masks = sread::read($byte_num);
		$fi_x = sread::read(15);
		$fi_y = sread::read(15);
		$rs_ecc_codewords = ord(sread::read(1));
		$rso = sread::read(128);
		sread::close();

		$matrix_x_array=unpack("C*",$matx);
		$matrix_y_array=unpack("C*",$maty);
		$mask_array=unpack("C*",$masks);
		$rs_block_order=unpack("C*",$rso);
		$format_information_x2=unpack("C*",$fi_x);
		$format_information_y2=unpack("C*",$fi_y);
		$format_information_x1=array(0,1,2,3,4,5,7,8,8,8,8,8,8,8,8);
		$format_information_y1=array(8,8,8,8,8,8,8,8,7,5,4,3,2,1,0);
		$max_data_codewords=($max_data_bits >>3);

		$qrv = $qrcodedb->query('SELECT Data FROM qr WHERE Name = \'rsc' . $rs_ecc_codewords . '\'');
		sread::open(gzdecode(hex2bin($qrv['Data'])));

		$i=0;
		while ($i<256) {
			$rs_cal_table_array[$i]=sread::read( $rs_ecc_codewords );
			$i++;
		}
		sread::close();

		/* -- read frame data  -- */
		$qrv = $qrcodedb->query('SELECT Data FROM qr WHERE Name = \'qrvfr' . self::$qrcode_version . '\'');
		$frame_data = gzdecode(hex2bin($qrv['Data']));

		/*  --- set terminator */
		if ($total_data_bits<=$max_data_bits-4){
			$data_value[$data_counter]=0;
			$data_bits[$data_counter]=4;
		} else {
			if ($total_data_bits<$max_data_bits){
				$data_value[$data_counter]=0;
				$data_bits[$data_counter]=$max_data_bits-$total_data_bits;
			} else {
				if ($total_data_bits>$max_data_bits){
					throw new Exception('current qr specification can\'t hold ' . $total_data_bits . ' bytes. maximum for this qr is ' . $max_data_bits . ' bytes.' );
				}
			}
		}

		/* ----divide data by 8bit */
		$i=0;
		$codewords_counter=0;
		$codewords[0]=0;
		$remaining_bits=8;
		while ($i<=$data_counter) {
			$buffer=@$data_value[$i];
			$buffer_bits=@$data_bits[$i];
			$flag=1;
			while ($flag) {
				if ($remaining_bits>$buffer_bits){  
					$codewords[$codewords_counter]=((@$codewords[$codewords_counter]<<$buffer_bits) | $buffer);
					$remaining_bits-=$buffer_bits;
					$flag=0;
				} else {
					$buffer_bits-=$remaining_bits;
					$codewords[$codewords_counter]=(($codewords[$codewords_counter] << $remaining_bits) | ($buffer >> $buffer_bits));
					if ($buffer_bits==0) {
						$flag=0;
					} else {
						$buffer= ($buffer & ((1 << $buffer_bits)-1) );
						$flag=1;   
					}

					$codewords_counter++;
					if ($codewords_counter<$max_data_codewords-1){
						$codewords[$codewords_counter]=0;
					}
					$remaining_bits=8;
				}
			}
			$i++;
		}
		if ($remaining_bits!=8) {
			$codewords[$codewords_counter]=$codewords[$codewords_counter] << $remaining_bits;
		} else {
			$codewords_counter--;
		}

		/* ----  set padding character */
		if ($codewords_counter<$max_data_codewords-1){
			$flag=1;
			while ($codewords_counter<$max_data_codewords-1){
				$codewords_counter++;
				if ($flag==1) {
					$codewords[$codewords_counter]=236;
				} else {
					$codewords[$codewords_counter]=17;
				}
				$flag=$flag*(-1);
			}
		}

		/* ---- RS-ECC prepare */
		$i=0;
		$j=0;
		$rs_block_number=0;
		$rs_temp[0]="";
		while($i<$max_data_codewords){
			$rs_temp[$rs_block_number].=chr($codewords[$i]);
			$j++;
			if ($j>=$rs_block_order[$rs_block_number+1]-$rs_ecc_codewords){
				$j=0;
				$rs_block_number++;
				$rs_temp[$rs_block_number]="";
			}
			$i++;
		}

		/*
		#
		# RS-ECC main
		#
		*/
		$rs_block_number=0;
		$rs_block_order_num=count($rs_block_order);
		while ($rs_block_number<$rs_block_order_num) {
			$rs_codewords=$rs_block_order[$rs_block_number+1];
			$rs_data_codewords=$rs_codewords-$rs_ecc_codewords;
			$rstemp=$rs_temp[$rs_block_number].str_repeat(chr(0),$rs_ecc_codewords);
			$padding_data=str_repeat(chr(0),$rs_data_codewords);
			$j=$rs_data_codewords;
			while($j>0){
				$first=ord(substr($rstemp,0,1));
				if ($first){
					$left_chr=substr($rstemp,1);
					$cal=$rs_cal_table_array[$first].$padding_data;
					$rstemp=$left_chr ^ $cal;
				} else {
					$rstemp=substr($rstemp,1);
				}
				$j--;
			}

			$codewords=array_merge($codewords,unpack("C*",$rstemp));
			$rs_block_number++;
		}

		/* ---- flash matrix */
		$i=0;
		while ($i<$max_modules_1side){
			$j=0;
			while ($j<$max_modules_1side){
				$matrix_content[$j][$i]=0;
				$j++;
			}
			$i++;
		}

		/* --- attach data */
		$i=0;
		while ($i<$max_codewords){
			$codeword_i=$codewords[$i];
			$j=8;
			while ($j>=1){
				$codeword_bits_number=($i << 3) +  $j;
				$matrix_content[ $matrix_x_array[$codeword_bits_number] ][ $matrix_y_array[$codeword_bits_number] ]=((255*($codeword_i & 1)) ^ $mask_array[$codeword_bits_number] ); 
				$codeword_i= $codeword_i >> 1;
				$j--;
			}
			$i++;
		}

		$matrix_remain=$matrix_remain_bit[self::$qrcode_version];
		while ($matrix_remain){
			$remain_bit_temp = $matrix_remain + ( $max_codewords <<3);
			$matrix_content[ $matrix_x_array[$remain_bit_temp] ][ $matrix_y_array[$remain_bit_temp] ] = ( 0 ^ $mask_array[$remain_bit_temp] );
			$matrix_remain--;
		}

		#--- mask select
		$min_demerit_score=0;
		$hor_master="";
		$ver_master="";
		$k=0;
		while($k<$max_modules_1side){
			$l=0;
			while($l<$max_modules_1side){
				$hor_master=$hor_master.chr($matrix_content[$l][$k]);
				$ver_master=$ver_master.chr($matrix_content[$k][$l]);
				$l++;
			}
			$k++;
		}
		$i=0;
		$all_matrix=$max_modules_1side*$max_modules_1side;

		while ($i<8){
			$demerit_n1=0;
			$ptn_temp=array();
			$bit= 1<< $i;
			$bit_r=(~$bit)&255;
			$bit_mask=str_repeat(chr($bit),$all_matrix);
			$hor = $hor_master & $bit_mask;
			$ver = $ver_master & $bit_mask;
			$ver_shift1=$ver.str_repeat(chr(170),$max_modules_1side);
			$ver_shift2=str_repeat(chr(170),$max_modules_1side).$ver;
			$ver_or=chunk_split(~($ver_shift1 | $ver_shift2),$max_modules_1side,chr(170));
			$ver_and=chunk_split(~($ver_shift1 & $ver_shift2),$max_modules_1side,chr(170));
			$hor=chunk_split(~$hor,$max_modules_1side,chr(170));
			$ver=chunk_split(~$ver,$max_modules_1side,chr(170));
			$hor=$hor.chr(170).$ver;
			$n1_search="/".str_repeat(chr(255),5)."+|".str_repeat(chr($bit_r),5)."+/";
			$n3_search=chr($bit_r).chr(255).chr($bit_r).chr($bit_r).chr($bit_r).chr(255).chr($bit_r);
			$demerit_n3=substr_count($hor,$n3_search)*40;
			$demerit_n4=floor(abs(( (100* (substr_count($ver,chr($bit_r))/($byte_num)) )-50)/5))*10;
			$n2_search1="/".chr($bit_r).chr($bit_r)."+/";
			$n2_search2="/".chr(255).chr(255)."+/";
			$demerit_n2=0;
			preg_match_all($n2_search1,$ver_and,$ptn_temp);
			foreach($ptn_temp[0] as $str_temp){ $demerit_n2+=(strlen($str_temp)-1); }
			$ptn_temp=array();
			preg_match_all($n2_search2,$ver_or,$ptn_temp);
			foreach($ptn_temp[0] as $str_temp){ $demerit_n2+=(strlen($str_temp)-1); }
			$demerit_n2*=3;
			$ptn_temp=array();
			preg_match_all($n1_search,$hor,$ptn_temp);
			foreach($ptn_temp[0] as $str_temp){ $demerit_n1+=(strlen($str_temp)-2); }
			$demerit_score=$demerit_n1+$demerit_n2+$demerit_n3+$demerit_n4;
			if ($demerit_score<=$min_demerit_score || $i==0){
				$mask_number=$i;
				$min_demerit_score=$demerit_score;
			}
			$i++;
		}

		$mask_content=1 << $mask_number;

		# --- format information
		$format_information_value=(($ec << 3) | $mask_number);
		$format_information_array=array("101010000010010","101000100100101","101111001111100","101101101001011","100010111111001","100000011001110","100111110010111","100101010100000","111011111000100","111001011110011","111110110101010","111100010011101","110011000101111","110001100011000","110110001000001","110100101110110","001011010001001","001001110111110","001110011100111","001100111010000","000011101100010","000001001010101","000110100001100","000100000111011","011010101011111","011000001101000","011111100110001","011101000000110","010010010110100","010000110000011","010111011011010","010101111101101");
		$i=0;
		while ($i<15){
			$content=substr($format_information_array[$format_information_value],$i,1);
			$matrix_content[$format_information_x1[$i]][$format_information_y1[$i]]=$content * 255;
			$matrix_content[$format_information_x2[$i+1]][$format_information_y2[$i+1]]=$content * 255;
			$i++;
		}

		$out="";
		$mxe=$max_modules_1side;
		$i=0;
		while ($i<$mxe){
			$j=0;
			while ($j<$mxe){
				if ($matrix_content[$j][$i] & $mask_content){
					$out.="1";
				} else {
					$out.="0";
				}
				$j++;
			}
			$out.="\n";
			$i++;
		}

		$out = $out | $frame_data;

		// Encode data to image
		$img_data_array=explode("\n",$out);
		$img_c=count($img_data_array)-1;
		$image_size=$img_c;
		$output_size=($img_c+(self::$qrcode_quiet_zone)*2)*self::$qrcode_module_size;
		$img=ImageCreate($image_size,$image_size);
		$white = ImageColorAllocate ($img, 255, 255, 255);
		$black = ImageColorAllocate ($img, 0, 0, 0);
		$im=ImageCreate($output_size,$output_size);
		$white2 = ImageColorAllocate ($im ,255,255,255);
		ImageFill($im,0,0,$white2);
		$y=0;
		foreach($img_data_array as $row){
			$x=0;
			while ($x<$image_size){
				if (substr($row,$x,1)=="1"){
					ImageSetPixel($img,$x,$y,$black);
				}
				$x++;
			}
			$y++;
		}
		$quiet_zone_offset=(self::$qrcode_quiet_zone)*(self::$qrcode_module_size);
		$image_width=$image_size*(self::$qrcode_module_size);
		ImageCopyResized($im,$img,$quiet_zone_offset ,$quiet_zone_offset,0,0,$image_width ,$image_width ,$image_size,$image_size);
		ob_start();
		ImageJPEG($im);
		imagedestroy($im);
		$img_result = ob_get_clean();

		return Array(
			'image'		=> 'data:image/jpg;base64,' . base64_encode($img_result),
			'url'		=> $qrcode_data_string,
			'secret'	=> self::$secret
		);
	}

}

/* class to emulate fread */
class sread {
	static $position = 0;
	static $data;

	public static function open($data) {
		self::$data = $data;
	}

	public static function read($bytes) {
		if( (self::$position + $bytes ) >= strlen(self::$data) ) {
			$data = substr(self::$data, self::$position);
		} else {
			$data = substr(self::$data, self::$position, $bytes);
		}
		self::$position = self::$position + strlen($data);
		return $data;
	}

	public static function close() {
		self::$data = null;
		self::$position = 0;
	}
}


/* class to handle qrcode database */
class qrcodedb {

	var $db;
	var $mode;

	function query($sql) {
		$result = $this->db->query($sql);
		return $result->fetchArray();
	}

	function __construct($filename, $live = true, $password = null) {
		if($live === true && !file_exists($filename)) {
			throw new Exception('data does not exist');
		} else {
			$mode = SQLITE3_OPEN_READONLY;
			if($live === false && !file_exists($filename)) {
				$mode = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
			} elseif( $live === false && file_exists($filename)) {
				$mode = SQLITE3_OPEN_READWRITE;
			}
			try{
				if($password === null) {
					$this->db = new SQLite3($filename, $mode );
				} else {
					$this->db = new SQLite3($filename, $mode, $password );
				}
				if($mode == (SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE)) {
					$this->mode = 'c';
//					$this->files('qr');
				} elseif ($mode == SQLITE3_OPEN_READWRITE) {
					$this->mode = 'w';
				} elseif ($mode == SQLITE3_OPEN_READONLY) {
					$this->mode = 'r';
				} else {
					throw new Exception('invalid access mode');
				}
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}
	}

	function __destruct() {
		if(!empty($this->mode)) {
			$this->db->close();
			$this->mode = null;
			$this->db = null;
		}
	}
}
