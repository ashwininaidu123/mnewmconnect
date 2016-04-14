<?php

// generate some random data:

srand((double)microtime()*1000000);

$max = 80;
$data = array();
$data1 = array();
$data2 = array();
$data3 = array();
$data4 = array();
for( $i=0; $i<7; $i++ )
{
  $data[] = rand(0,$max);
  $data1[] = rand(0,$max);
  $data2[] = rand(0,$max);
  $data3[] = rand(0,$max);
  $data4[] = rand(0,$max);
}

// use the chart class to build the chart:
include_once( 'open-flash-chart.php' );
$g = new graph();

// Spoon sales, March 2007
$g->title( 'Spoon sales '. date("Y"), '{font-size: 26px;}' );

$g->set_data( $data);
$g->set_data( $data1);
$g->set_data( $data2);
$g->set_data( $data3);
$g->set_data( $data4);
$g->line_hollow( 2, 4, '0x0000CC', 'Tapan', 10 );
$g->line_hollow( 2, 4, '0xCC0000', 'Dinesh', 10 );
$g->line_hollow( 2, 4, '0x00CC00', 'Nil', 10 );
$g->line_hollow( 2, 4, '0xcccccc', 'Siva', 10 );
$g->line_hollow( 2, 4, '0x000000', 'Arif', 10 );
// label each point with its value
$g->set_x_labels( array('sun','mon','tues','wed','thur','fri','sat' ),7 );

// set the Y max
$g->set_y_max( 100 );
// label every 20 (0,20,40,60)
$g->y_label_steps( 10 );

// display the data
echo $g->render();
?>
