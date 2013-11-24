<?
	require_once('config.php');
	session_start();

	class Grid
	{
		// pocet stranek
		public	$mTotalPages;
		// pocet polozek
		public $mItemCount;
		// index vracene stranky
		public $mReturnedPage;

		// database handler
		private $mMysqli;
		private $grid;

		// konstruktor
		function __construct()
		{
			// vytvorit pripojeni
			$this->mMysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
			$this->mItemCount=$this->countAllRecords();
		}

		// destruktor
		function __destruct()
		{
			$this->mMysqli->close();
		}

		// nacist stranku a ulozit ji do grid
		//
		public function readPage($page)
		{
			$queryString=$this->createSubpageQuery('select * from dzeit',$page);

			// spustim dotaz
			if($result = $this->mMysqli->query($queryString))
			{
				while($wor = $result->fetch_assoc())
				{
					$this->grid .= '<row>';
					foreach($row as $name=>$val)
						$this->grid .= '<' . $name . '>' . htmlentities($val) . '</' . $name . '>';
					$this->grid .= '</row>';
				}
			}
		}

		public function updateRecord($id,$on_promotion,$price,$name)
		{
			$id = $this->mMysqli->real_escape_string($id);
			$on_promotion = $this->mMysqli->real_escape_string($on_promotion);
			$price= $this->mMysqli->real_escape_string($price);
			$name= $this->mMysqli->real_escape_string($name);

			$queryString = "update dzeit set name='$name' where (id='$id')";

			$this->mMysqli->query($queryString);
		}

		public function getParamsXML()
		{
			$previous_page = ($this->mReturnedPage==1) ? '' : $this->mReturnedPage-1;
			$next_page = ($this->mTotalPages == $this->mReturnedPage) ? '' : $this->mReturnedPage+1;

			return '<params>'.
				"<returned_page>$this->mReturnedPage</returned_page>".
				"<total_pages>$this->mTotalPages</total_pages>".
				"<items_count>$this->mItemsCount</items_count>".
				"<previous_page>$previous_page</previous_page>".
				"<next_page>$next_page</next_page>".
				"</params>";

		}

		public function getGridXML()
		{
			return '<grid>'.$this->grid.'</grid>';
		}

		private function countAllRecords()
		{
			// kdyz pocet zaznamu neni nakesovat v session promenne, tak ji prectu z databaze
			if(!isset($_SESSION['record_count']))
			{
				$count_query = 'select count(*) from dzeit';
				if($result=$this->mMysqli->query($count_query))
				{
					$row=$result->fetch_row();
					$_SESSION['record_count']=$row[0];
					$result->close();
				}
			}
			return $_SESSION['record_count'];
		}

		private function createSubpageQuery($queryString,$pageNo)
		{
			if($this->mItemsCount<=ROWS_PER_VIEW)
			{
				$pageNo=1;
				$this->mTotalPages=1;
			}
			else
			{
				$this->mTotalPages=ceil($this->mItemsCount/ROWS_PER_VIEW);
				$start_page=($pageNo-1)*ROWS_PER_VIEW;
				$queryString.=' limit ' .$start_page.','.ROWS_PER_VIEW;
			}
			$this->mReturnedPage=$pageNo;
			return $queryString;
		}

	}
?>

