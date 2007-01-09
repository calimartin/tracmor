<?php

	class QDataGridColumnToggle extends QControl {
		
		// The Column Toggle Menu QPanel
		public $pnlColumnToggleMenu;
		
		// The array of column labels found inside the QPanel
		protected $arrColumnLabels;
		
		// JAVASCRIPT
		protected $strJavaScripts = 'datagrid_column_toggle.js';
		
		//////////
		// Methods
		//////////
		public function __construct($objParentObject, $strControlId = null) {
			try {
				parent::__construct($objParentObject, $strControlId);
			} catch (QCallerException  $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
			
			$this->pnlColumnToggleMenu_Create();
		}
		
		public function ParsePostData() {}
		public function Validate() {return true;}
		public function GetJavaScriptAction() {return 'onclick';}
		
		public function GetControlHtml() {
			
			// Create the Column Labels. They cannot be created when creating the datagrid because columns can be added after instantiation.
			$this->arrColumnLabels = array();
			if ($this->objParentControl->ColumnArray) {
				foreach ($this->objParentControl->ColumnArray as $objColumn) {
					$lblColumn = new QLabel($this->pnlColumnToggleMenu);
					$lblColumn->Text = $objColumn->Name;
					$lblColumn->ActionParameter = $objColumn->Name;
					$lblColumn->AddAction(new QClickEvent(), new QToggleDisplayAction($this->pnlColumnToggleMenu));
					$lblColumn->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'lblColumn_Click'));
					$lblColumn->AddAction(new QMouseOverEvent(), new QJavascriptAction('this.style.backgroundColor=\'#EEEEEE\';'));
					$lblColumn->AddAction(new QMouseOutEvent(), new QJavaScriptAction('this.style.backgroundColor=\'#FFFFFF\';'));
					if ($objColumn->Display) {
						$lblColumn->FontBold = true;
					}
					
					// Style
					$lblColumn->TagName = 'div';
					$lblColumn->SetCustomStyle('margin', '4px 4px 4px 8px');
					$lblColumn->SetCustomStyle('cursor', 'pointer');
					array_push($this->arrColumnLabels, $lblColumn);
				}
			}
			
			// Setting display to false again. This is to fix the problem when changing pagination or sorting while the menu is open.
			$this->pnlColumnToggleMenu->Display = false;
			
			// Render the Column Toggle Menu
			$strToReturn = $this->pnlColumnToggleMenu->Render(false, 'BorderWidth=1');
			
			return $strToReturn;
		}
		
		public function pnlColumnToggleMenu_Create() {
			
			// Currently, setting AutoRenderChildren to true will not work because it creates a new set of labels on every AJAX action
			// but does not get rid of the original labels.
			$this->pnlColumnToggleMenu = new QPanel($this);
			$this->pnlColumnToggleMenu->Name = 'Toggle Menu';
			$this->pnlColumnToggleMenu->Width = 130;
			$this->pnlColumnToggleMenu->SetCustomStyle('padding', '2px');
			$this->pnlColumnToggleMenu->BackColor = 'white';
			$this->pnlColumnToggleMenu->Template = __INCLUDES__.'/qcodo/qform/pnl_column_toggle.tpl.php';
			$this->pnlColumnToggleMenu->Display = false;
			$this->pnlColumnToggleMenu->Text = 'Show/Hide Columns:';
			// $this->pnlColumnToggleMenu->AutoRenderChildren = true;
		}
		
		// Toggle whether a column is being displayed or not
		public function lblColumn_Click($strFormId, $strControlId, $strParameter) {
			$objColumn = $this->objParentControl->GetColumnByName($strParameter);
			if (!$objColumn->Display) {
				$objColumn->Display = true;
			}
			else {
				$objColumn->Display = false;
			}
		}
		
		/////////////////////////
		// Public Properties: GET
		/////////////////////////
		public function __get($strName) {
			switch ($strName) {
				
				case 'ColumnLabels': return $this->arrColumnLabels;

				default:			
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}


		/////////////////////////
		// Public Properties: SET
		/////////////////////////
		public function __set($strName, $mixValue) {
			$this->blnModified = true;

			switch ($strName) {
				
				case 'ColumnLabels':
					try {
						$this->arrColumnLabels = QType::Cast($mixValue, QType::ArrayType);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				default:
					try {
						return (parent::__set($strName, $mixValue));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					break;
			}
		}
	}

?>