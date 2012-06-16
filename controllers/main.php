<?php
	class Test1Main {

		function index(){
			global $sfp;

			$sfp->Msg->set('error', "Wellcome to Framepress");
			$sfp->Msg->set('info', "Wellcome to Framepress");
			$sfp->Msg->set('success', "Wellcome to Framepress");
			$sfp->Msg->set('warning', "Wellcome to Framepress");

			if( $sfp->Session->check('bar') ) {
				//set bar to the view
				$sfp->View->set('bar', $sfp->Session->read('bar') );
				//remove from session
				$sfp->Session->delete('bar');
			}
		}

		function save(){
			global $sfp;

			//write in session
			$sfp->Session->write('bar', 'Hello World!');

			//redirect to index function ( yes redirect using headers!! )
			$sfp->redirect(array('function' => 'index'));
		}

	}
?>
