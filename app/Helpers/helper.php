<?php

function order_number()
{
	return 'LKL-'.rand_num(2).time().rand_num();
}

function cancel_number()
{
	return 'LKL-CNL-'.rand_num(2).time().rand_num();
}

function seller_number()
{
	return 'SN-'.rand_num(2).time().rand_num();
}

function rand_char()
{
	$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	return substr(str_shuffle($permitted_chars), 0, 8);
}

function rand_num($count = 5)
{
	$permitted_chars = '0123456789';
	return substr(str_shuffle($permitted_chars), 0,$count);
}

function date_now()
{
	return date('Y-m-d H:i:s');
}