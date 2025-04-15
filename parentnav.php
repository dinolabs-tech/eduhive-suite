 <!-- Sidebar -->
 <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
       <?php include('logo_header.php');?>
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item">
                <a href="parent.php">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>                  
                </a>
              </li>
             
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#bursary">
                <i class="fas fa-hand-holding-usd"></i>
                  <p>Bursary</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="bursary">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="bpayment.php">
                        <span class="sub-item">Deposit</span>
                      </a>
                    </li>
                    <li>
                      <a href="paymentstatus.php">
                        <span class="sub-item">Payment Status</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>

           


              <li class="nav-item">
                <a href="viewtimetable.php">
                  <i class="fas fa-th-list"></i>
                  <p>Class Schedule</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="idcard.php">
                  <i class="fas fa-id-card"></i>
                  <p>Download ID Card</p>
                </a>
              </li>

           

            
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->