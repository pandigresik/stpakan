<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Email extends CI_Email
{
    public function __construct(array $config = array()){
        parent::__construct($config);
    }

    /*Attach file base64*/
    public function attachBase64($file, $disposition = '', $newname = NULL, $mime = '')
    {
        if ($mime === '')
        {
            if (strpos($file, '://') === FALSE && ! file_exists($file))
            {
                $this->_set_error_message('lang:email_attachment_missing', $file);
                return FALSE;
            }

            if ( ! $fp = @fopen($file, 'rb'))
            {
                $this->_set_error_message('lang:email_attachment_unreadable', $file);
                return FALSE;
            }

            $file_content = stream_get_contents($fp);
            $mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
            fclose($fp);
        }
        else
        {
            $file_content =& $file; // buffered file
            $file_content = ($this->_encoding == 'base64') ? $file_content   :  chunk_split(base64_encode($file_content));
        }

        $this->_attachments[] = array(
            'name'		=> array($file, $newname),
            'disposition'	=> empty($disposition) ? 'attachment' : $disposition,  // Can also be 'inline'  Not sure if it matters
            'type'		=> $mime,
            'content'	=> $file_content
        );

        return $this;
    }
}

