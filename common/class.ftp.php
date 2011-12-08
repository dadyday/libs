<?php
	class LocalDirectoryIterator extends DirectoryIterator {
		
		function download($path) {
			return copy($this -> getPathname(), $path);
		}
		function upload($path, $filename = '') {
			if (empty($filename)) $filename = basename($path);
			return copy($path, dirname($this -> getPathname()) . '/' . $filename);
		}
		
		function copyTo($path) {
			return copy($this -> getPathname(), $path);
		}
		
		function delete() {
			return unlink($this -> getPathname());
		}
	}

	class FtpDirectoryIterator extends DirectoryIterator {
		protected $conHandle = null;
		protected $host = '';
		public $pos = 0;
		public $arrFiles = array();
		public $arrLog = array();
		
		protected $name = '';
		protected $path = '';
		protected $size = 0;
		protected $isDir = false;
		protected $created = 0;
		protected $changed = 0;
		
		
		function __construct($host = '', $user = '', $pass = '', $path = '') {
			//parent::__construct();
			if (!empty($host)) $this -> connect($host, $user, $pass, $path);
		}
		
		function __destruct() {
			$this -> disconnect();
		}
		
		function log($text) {
			//echo '<br>'.$text; flush();
			$this -> arrLog[] = date('Y-m-d H:i:s: ') . $text;
		}
		
		public function connect($host, $user, $pass, $dir = '') {
			$this -> conHandle = @ftp_connect($host, 21);
			if (!$this -> conHandle) throw new Exception('connection failed');
			
			$ok = @ftp_login($this -> conHandle, $user, $pass);
			if (!$ok) throw new Exception('login failed');
			
			ftp_set_option($this -> conHandle, FTP_TIMEOUT_SEC, 10);
			if (!ftp_pasv($this -> conHandle, false)) throw new Exception('passivmode disabling failed');
			
			$this -> host = 'ftps://' . $user . ':' . $pass . '@' . $host;
			if (empty($dir)) {
				$dir = ftp_pwd($this -> conHandle);
			}
			$this -> chdir($dir);
			
			return true;
		}
		
		function disconnect() {
			if ($this -> conHandle) ftp_close($this -> conHandle);
			$this -> conHandle = null;
			return true;
		}
		
		function chdir($dir) {
			if (!@ftp_chdir($this -> conHandle, $dir)) return false;
			
			$arrFiles = ftp_nlist($this -> conHandle, '');
			if ($arrFiles === false) return false;
			
			$this -> arrFiles = array();
			foreach ($arrFiles as $n => $file) {
				$this -> arrFiles[] = basename($file);
			}
			$this -> pos = 0;
			$this -> directory = ftp_pwd($this -> conHandle);
			return true;
		}
		
		protected function loadFileInfo() {
			if ($this -> arrFiles[$this -> pos] == $this -> name) return;
			
			$this -> name = $this -> arrFiles[$this -> pos];
			$this -> size = ftp_size($this -> conHandle, $this -> name);
			
			$this -> isDir = $this -> size < 0;
			//$this -> isDir = @ftp_chdir($this -> conHandle, $this -> name);
			//if ($this -> isDir) @ftp_cdup($this -> conHandle);
			
			$this -> created = ftp_mdtm($this -> conHandle, $this -> name);
		}
		
		function copyTo($path) {
			return $this -> download($path);
		}
		function download($path) {
			$remote = $this -> arrFiles[$this -> pos];
			$local = $path . '/' . $remote;
			return @ftp_get($this -> conHandle, $local, $remote, FTP_BINARY);
		}
		function delete() {
			return @ftp_delete($this -> conHandle, $this -> arrFiles[$this -> pos]);
		}
		function upload($path, $filename = '') {
			if (empty($filename)) $filename = basename($path);
			
			$remote = $this -> directory . '/' . $filename;
			$local = $path;
			return ftp_put($this -> conHandle, $remote, $local, FTP_BINARY);
		}
		
	// Schnittstellenmethoden aus DirectoryIterator
		function key() {
			return $this -> arrFiles[$this -> pos];
		}
		function getPath() {
			return $this -> directory;
		}
		function getPathname() {
			return $this -> host . $this -> directory . $this -> arrFiles[$this -> pos];
		}
		function getFilename() {
			return $this -> arrFiles[$this -> pos];
		}
		function rewind() {
			$this -> pos = 0;
		}
		function next() {
			$this -> pos ++;
		}
		function valid() {
			return $this -> pos < count($this -> arrFiles);
		}

		function isDir() {
			$this -> loadFileInfo();
			return $this -> isDir;
		}
		function isFile() {
			$this -> loadFileInfo();
			return !$this -> isDir;
		}
		function isDot() {
			$this -> loadFileInfo();
			return $this -> isDir && $this -> name{0} == '.';
		}
/*
	DirectoryIterator::getATime --  Get last access time of file 
	DirectoryIterator::getCTime --  Get inode modification time of file 
	DirectoryIterator::getChildren --  Returns an iterator for the current entry if it is a directory 
	DirectoryIterator::getGroup --  Get file group 
	DirectoryIterator::getInode --  Get file inode 
	DirectoryIterator::getMTime --  Get last modification time of file 
	DirectoryIterator::getOwner --  Get file owner 
	DirectoryIterator::getPerms --  Get file permissions 
	DirectoryIterator::getSize --  Get file size 
	DirectoryIterator::getType --  Get file type 
	DirectoryIterator::isExecutable --  Returns true if file is executable 
	DirectoryIterator::isLink --  Returns true if file is symbolic link 
	DirectoryIterator::isReadable --  Returns true if file can be read 
	DirectoryIterator::isWritable --  Returns true if file can be written 
*/
	}










	function ssh_disconnect($reason, $message, $language) {
		  throw new Exception('disconnected');
	}




	class SFtpDirectoryIterator extends FtpDirectoryIterator {
		protected $sftpHandle = null;
		
		public function connect($host, $user, $pass, $dir = '') {
			$methods = array(
				'kex' => 'diffie-hellman-group1-sha1',
				'client_to_server' => array(
					'crypt' => '3des-cbc',
					'comp' => 'none'
				),
				'server_to_client' => array(
					'crypt' => 'aes256-cbc,aes192-cbc,aes128-cbc',
					'comp' => 'none'
				)
			);
			$callbacks = array('disconnect' => 'ssh_disconnect');
		
			$this -> log('connecting ' . $host);
			$this -> conHandle = ssh2_connect($host, 22);
			//$this -> conHandle = ssh2_connect($host, 22, $methods, $callbacks);
			if (!$this -> conHandle) throw new Exception('connection failed');
			
			$this -> log('login ' . $user);
			$ok = ssh2_auth_password($this -> conHandle, $user, $pass);
			if (!$ok) throw new Exception('login failed');
			
			$this -> log('etablish sftp');
			$this -> sftpHandle = ssh2_sftp($this -> conHandle);
			if (!$this -> sftpHandle) throw new Exception('etablishing sftp failed');
			
			$this -> host = 'ssh2.sftp://' . $this -> sftpHandle . '/';
			
			if (empty($dir)) {
				$dir = '/';
			}
			if (!$this -> chdir($dir)) throw new Exception('chdir failed');
			
			return true;
		}
		
		function disconnect() {
			$this -> sftpHandle = null;
			$this -> conHandle = null;
			return true;
		}
		
		function chdir($dir) {
			$this -> log('change directory ' . $dir);
			$remoteDir = opendir($this -> host . $dir);
			if (!$remoteDir) {
				$this -> log('ERROR - change directory failed');
				return false;
			}
			
			$this -> arrFiles = array();
			
			$this -> log('reading filelist');
			$file = readdir($remoteDir);
			while ($file !== false) {
				$this -> arrFiles[] = $file;
				$file = readdir($remoteDir);
			};
			closedir($remoteDir);
			$this -> pos = 0;
			$this -> directory = $dir;
			return true;
		}
		
		protected function loadFileInfo() {
			if ($this -> arrFiles[$this -> pos] == $this -> name) return;
			
			$this -> log('loading fileinfo ' . $this -> arrFiles[$this -> pos]);
			$this -> name = $this -> arrFiles[$this -> pos];
			$arrInfo = ssh2_sftp_lstat($this -> sftpHandle, $this -> arrFiles[$this -> pos]);
			
			$this -> size = $arrInfo['size'];
			$this -> isDir = ($arrInfo['mode'] & 0xc000) == 0x4000;
			$this -> created = $arrInfo['mtime'];
			$this -> changed = $arrInfo['atime'];
		}
		
		function copyTo($path) {
			$this -> log('copy file ' . $this -> directory . $this -> arrFiles[$this -> pos] . ' to ' . $path);
			//ssh2_scp_recv($this -> conHandle, $this -> directory . $this -> arrFiles[$this -> pos], $path);
			copy($this -> host . $this -> directory . $this -> arrFiles[$this -> pos], $path);
		}
		
	}

?>