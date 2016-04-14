
<div class="content-wrapper">
<section class="content-header">
  <h1>Dashboard
  </h1>
</section>
<section class="content">
  <div class="row">
    <?php  foreach ($totalcount as $key => $value) { ?> 
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $value['offers'] ?></h3>
          <p>OFFERS</p>
        </div>
        <div class="icon">
          <i class="fa fa-fw fa-gift"></i>
        </div>
        <!--
          <a href="ListOffers/0" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          -->
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3><?php echo $value['likes'] ?></h3>
          <p>LIKES</p>
        </div>
        <div class="icon">
          <i class="fa fa-thumbs-o-up"></i>
        </div>
        <!--
          <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          -->
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?php echo $value['visits'] ?></h3>
          <p>VISITS</p>
        </div>
        <div class="icon">
          <i class="fa fa-fw fa-users"></i>
        </div>
        <!--
          <a href="SiteVisits/0" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          -->
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
          <h3><?php echo $value['referrals'] ?></h3>
          <p>REFERRALS</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        <!--
          <a href="Referrals/0" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          -->
      </div>
    </div>
    <!-- ./col -->
  </div>
  <!-- /.row -->
  <?php } ?>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Monthly Recap Report</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-8">
              <p class="text-center">
                <strong>Visites and Likes</strong>
              </p>
              <div class="chart">
                <!-- Sales Chart Canvas -->
                <canvas id="salesChart" style="height: 180px;"></canvas>
              </div>
              <!-- /.chart-responsive -->
            </div>
            <!-- /.col -->
            <div class="col-md-4">
              <p class="text-center">
                <strong>Goal Completion</strong>
              </p>
              <div class="progress-group">
                <span class="progress-text">Add Products to Cart</span>
                <span class="progress-number"><b>160</b>/200</span>
                <div class="progress sm">
                  <div class="progress-bar progress-bar-aqua" style="width: 80%"></div>
                </div>
              </div>
              <!-- /.progress-group -->
              <div class="progress-group">
                <span class="progress-text">Complete Purchase</span>
                <span class="progress-number"><b>310</b>/400</span>
                <div class="progress sm">
                  <div class="progress-bar progress-bar-red" style="width: 80%"></div>
                </div>
              </div>
              <!-- /.progress-group -->
              <div class="progress-group">
                <span class="progress-text">Visit Premium Page</span>
                <span class="progress-number"><b>480</b>/800</span>
                <div class="progress sm">
                  <div class="progress-bar progress-bar-green" style="width: 80%"></div>
                </div>
              </div>
              <!-- /.progress-group -->
              <div class="progress-group">
                <span class="progress-text">Send Inquiries</span>
                <span class="progress-number"><b>250</b>/500</span>
                <div class="progress sm">
                  <div class="progress-bar progress-bar-yellow" style="width: 80%"></div>
                </div>
              </div>
              <!-- /.progress-group -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- ./box-body -->
        <div class="box-footer">
          <div class="row">
            <div class="col-sm-3 col-xs-6">
              <div class="description-block border-right">
                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                <h5 class="description-header">$35,210.43</h5>
                <span class="description-text">TOTAL REVENUE</span>
              </div>
              <!-- /.description-block -->
            </div>
            <!-- /.col -->
            <div class="col-sm-3 col-xs-6">
              <div class="description-block border-right">
                <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                <h5 class="description-header">$10,390.90</h5>
                <span class="description-text">TOTAL COST</span>
              </div>
              <!-- /.description-block -->
            </div>
            <!-- /.col -->
            <div class="col-sm-3 col-xs-6">
              <div class="description-block border-right">
                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 20%</span>
                <h5 class="description-header">$24,813.53</h5>
                <span class="description-text">TOTAL PROFIT</span>
              </div>
              <!-- /.description-block -->
            </div>
            <!-- /.col -->
            <div class="col-sm-3 col-xs-6">
              <div class="description-block">
                <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> 18%</span>
                <h5 class="description-header">1200</h5>
                <span class="description-text">GOAL COMPLETIONS</span>
              </div>
              <!-- /.description-block -->
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-footer -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->	
  <h3>Properties</h3>
  <div class="row">
  <?php $j=1;  for($i=0;$i<sizeof($prop);$i++) { ?>
  <!--   - ---------- Dashboard tab starts here --------------- -->
  <div class="col-md-4">
  <!-- Widget: user widget style 1 -->
  <div class="box box-widget widget-user-2">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <?php if($j==1){ $j++; ?>
    <div class="widget-user-header bg-yellow">
      <?php }elseif($j==2){ $j++; ?>
      <div class="widget-user-header bg-green">
        <?php }elseif($j==3){ $j=1;?>
        <div class="widget-user-header bg-red">
          <?php }?>
          <div class="widget-user-image">
            <img class='img-circle' alt='uploaded image'  height=\"75\" width=\"75\"  src="<?=base_url()?>/uploads/<?php echo $prop[$i]['propertyicon'] ?>">
          </div>
          <!-- /.widget-user-image -->
          <h3 class="widget-user-username"><?php echo $prop[$i]['propertyname'] ?></h3>
          <h5 class="widget-user-desc"><?php echo $prop[$i]['propdesc'] ?></h5>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-stacked">
            <li><a href="ListSite/0">Sites <span class="pull-right badge bg-blue"><?php echo $prop[$i]['sites'] ?></span></a></li>
            <li><a href="#">Likes <span class="pull-right badge bg-aqua"><?php echo $prop[$i]['likes'] ?></span></a></li>
            <li><a href="Referrals/0">Refferals <span class="pull-right badge bg-green"><?php echo $prop[$i]['referrals'] ?></span></a></li>
            <li><a href="ListOffers/0">Offers <span class="pull-right badge bg-red"><?php echo $prop[$i]['offers'] ?></span></a></li>
          </ul>
        </div>
      </div>
      <!-- /.widget-user -->
    </div>
    <!-- /.col -->
    <?php } ?>							  
  </div>
  <h3>Show Homes</h3>
  <div class="row">
  <?php $j=1;  for($i=0;$i<sizeof($sitevist);$i++) { ?>
  <!--   - ---------- Dashboard tab starts here --------------- -->
  <div class="col-md-4">
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user-2">
      <!-- Add the bg color to the header using any of the bg-* classes -->
      <?php if($j==1){ $j++; ?>
      <div class="widget-user-header bg-blue">
        <?php }elseif($j==2){ $j++; ?>
        <div class="widget-user-header bg-aqua">
          <?php }elseif($j==3){ $j=1;?>
          <div class="widget-user-header bg-yellow">
            <?php }?>
            <div class="widget-user-image">
              <img class='img-circle' alt='uploaded image'  height=\"75\" width=\"75\"  src="<?=base_url()?>/uploads/<?php echo $sitevist[$i]['siteicon'] ?>">
            </div>
            <!-- /.widget-user-image -->
            <h3 class="widget-user-username"><?php echo $sitevist[$i]['sitename'] ?></h3>
            <h5 class="widget-user-desc"><?php echo $sitevist[$i]['propertyname'] ?></h5>
          </div>
          <div class="box-footer no-padding">
            <ul class="nav nav-stacked">
              <li><a href="SiteVisits/0">Visits <span class="pull-right badge bg-blue"><?php echo $sitevist[$i]['visits'] ?></span></a></li>
              <li><a href="#">Likes <span class="pull-right badge bg-aqua"><?php echo $sitevist[$i]['likes'] ?></span></a></li>
              <li><a href="Referrals/0">Refferals <span class="pull-right badge bg-green"><?php echo $sitevist[$i]['referrals'] ?></span></a></li>
              <li><a href="ListOffers/0">Offers <span class="pull-right badge bg-red"><?php echo $sitevist[$i]['offers'] ?></span></a></li>
            </ul>
          </div>
        </div>
        <!-- /.widget-user -->
      </div>
      <!-- /.col -->
      <?php  } ?>		
    </div>
 
    <!-- ---------- Dashboard tab Ends here --------------- --->
    <div class="modal fade" id="modal-responsive" aria-hidden="true"></div>
  </div>
</section>
    <!-- jQuery 2.1.4 -->
    <script src="system/application/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- page script -->
    <script>
        //--------------
        //- AREA CHART -
        //--------------
$(function () {

	      var labels = [];
      //    labels = ["January", "February", "March", "April", "May", "June", "July" , "August", "September", "October", "November", "December"];
         labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul" , "Aug", "Sep", "Oct", "Nov", "Dec"];
          var myobj =<?php echo(json_encode($visitcount)); ?>;	  
          var myobj2 =<?php echo(json_encode($likecount)); ?>;
     //   alert (JSON.stringify(myobj)); 
     //  alert (JSON.stringify(myobj2)); 
           var key1 = [];
           var key2 = [];

			lengthfind = Object.keys(labels).length;
			
			function jval(p){
				for(var k in p)
				  {
					  return p[k];
				  }
			 }
			key1 = Object.keys(myobj);
			
			key2 = Object.keys(myobj2);
	        
			var arr1 = [];
			for (i = 0; i < lengthfind; i++) {
			  if(labels[i]==key1[i]){
                 arr1.push(jval(myobj[key1[i]]));
			   }else{
				   arr1.push(0);
			   }
			}
		   var arr2 = [];
			for (i = 0; i < lengthfind; i++) {
			  if(labels[i]==key2[i]){
                 arr2.push(jval(myobj2[key2[i]]));
			   }else{
				  arr2.push(0);
			   }
			} 
        // Get context with jQuery - using jQuery's .get() method.
        var areaChartCanvas = $("#salesChart").get(0).getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var areaChart = new Chart(areaChartCanvas);

        var areaChartData = {
          labels:labels,
          datasets: [
            {
              label: "Visits",
              fillColor: "rgba(210, 214, 222, 1)",
              strokeColor: "rgba(210, 214, 222, 1)",
              pointColor: "rgba(210, 214, 222, 1)",
              pointStrokeColor: "#c1c7d1",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(220,220,220,1)",
              data: arr1
            },
            {
              label: "Likes",
              fillColor: "rgba(60,141,188,0.9)",
              strokeColor: "rgba(60,141,188,0.8)",
              pointColor: "#3b8bba",
              pointStrokeColor: "rgba(60,141,188,1)",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(60,141,188,1)",
              data: arr2
            }
          ]
        };

       
        var areaChartOptions = {
          //Boolean - If we should show the scale at all
          showScale: true,
          //Boolean - Whether grid lines are shown across the chart
          scaleShowGridLines: false,
          //String - Colour of the grid lines
          scaleGridLineColor: "rgba(0,0,0,.05)",
          //Number - Width of the grid lines
          scaleGridLineWidth: 1,
          //Boolean - Whether to show horizontal lines (except X axis)
          scaleShowHorizontalLines: true,
          //Boolean - Whether to show vertical lines (except Y axis)
          scaleShowVerticalLines: true,
          //Boolean - Whether the line is curved between points
          bezierCurve: true,
          //Number - Tension of the bezier curve between points
          bezierCurveTension: 0.3,
          //Boolean - Whether to show a dot for each point
          pointDot: false,
          //Number - Radius of each point dot in pixels
          pointDotRadius: 4,
          //Number - Pixel width of point dot stroke
          pointDotStrokeWidth: 1,
          //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
          pointHitDetectionRadius: 20,
          //Boolean - Whether to show a stroke for datasets
          datasetStroke: true,
          //Number - Pixel width of dataset stroke
          datasetStrokeWidth: 2,
          //Boolean - Whether to fill the dataset with a color
          datasetFill: true,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
          //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          maintainAspectRatio: true,
          //Boolean - whether to make the chart responsive to window resizing
          responsive: true
        };

        //Create the line chart
        areaChart.Line(areaChartData, areaChartOptions);
 });
  </script>
