<?php
   class TestFirst {

      function before_filter(){
         //do something before a function call
      }

      function index(){
         global $framepress_test;

         $framepress_test->Session->write('pepe', array('somavalue'));

         $framepress_test->redirect(array('controller'=>'first', 'function'=>'page2', 'some_arg', 'sec_arg'));
      }

      function page2 ( $foo='some_arg', $bar='sec_arg' ) {
         global $framepress_test;

         $framepress_test->View->layout('alayout');

         $framepress_test->Msg->general('hello World');

         if ( $framepress_test->Session->check('pepe')) {

            $framepress_test->View->set('bar', $bar);

         }

         $framepress_test->View->set('foo', $foo);
      }

      function after_filter(){
         //do something after a function call
      }

   }
?>
