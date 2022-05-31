<?php 
namespace App\Output;

class Output
{
	public function display(string $response)
	{
		echo $response;
	}

	public function write(string $response, string $filename, bool $append = false)
	{
		if($append)
		{
			file_put_contents($response,$filename,FILE_APPEND);
		}
		else
		{
			file_put_contents($response,$filename);
		}
	}

}