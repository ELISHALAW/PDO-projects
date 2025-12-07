<?php 

require __DIR__ . '/headandFoot/head.php';

$stmt = $_db->prepare("SELECT * FROM review");
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);



$totalStmt = $_db->prepare("SELECT COUNT(*) AS total_reviews FROM review");
$totalStmt->execute();
$totalReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$num = 0;

$customerStmt = $_db->prepare('SELECT * FROM user LIMIT 5');
$customerStmt->execute();
$totalCustomers = $customerStmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!-- When other teammate want to modify please modify here -->
<div class="cards">
                <div class="card-single">
                    <div>
                        <h1><?= countAllCustomer() ?></h1>
                        <span>Customers</span>
                    </div>
                    <div>
                        <span>&#128101;</span>
                    </div>
                </div>
                <div class="card-single">
                    <div>
                        <h1><?= countAllOrder() ?></h1>
                        <span>Orders </span>
                    </div>
                    <div>
                        <span>&#128221;</span>
                    </div>
                </div>
                <div class="card-single">
                    <div>
                        <h1><?= countAllUnits() ?></h1>
                        <span>Sum of the Quantity</span>
                    </div>
                    <div>
                        <span>&#128722;</span>
                    </div>
                </div>
                <div class="card-single">
                    <div>
                        <h1>RM<?= countAllSubtotal() ?></h1>
                        <span>Income</span>
                    </div>
                    <div>
                        <span>&#128176;</span>
                    </div>
                </div>
            </div>
            <!-- When other teammate want to modify please modify this part -->
            <!-- When other teammate want to modify please modify this part -->
            <div class="recent-grid">  
                <div class="projects">
                    <div class="card">
                        <div class="card-header">
                            <h3>Review</h3>
                            <a href="reviewlist.php">See All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-response">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <td>Num</td>
                                        <td>Name</td>
                                        <td>Number of Star</td>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php foreach($reviews as $review): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                                if($num <= $totalReviews){
                                                    $num++;
                                                    echo $num; 
                                                    
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo e($review['name']) ?></td>
                                        <td><?php echo str_repeat('⭐', $review['number_of_star'] ?? 0); ?></td>
                                    </tr>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="customers">
                <div class="card"> 
                    <div class="card-header">
                        <h3> Customer</h3>
                        <a href="reviewlist.php">See All</a>
                    </div>
                    <div class="card-body">
                    <?php  foreach($totalCustomers as $totalCustomer) :?>
                        <div class="customer">
                        <div class="info">
                            <div>
                                <h4><?php echo e($totalCustomer['name']); ?></h4>
                                <small><?php echo e($totalCustomer['username']); ?></small>
                            </div>
                        </div>
                        <div class="contact">
                            <a href="mailto:<?php echo htmlspecialchars($totalCustomer['email'] ?? ''); ?>">
                                 <i class="las la-envelope"></i>
                            </a>
                            <!-- <span class="las la-envelope"></span> -->
                       </div>
                    </div>
                    <hr>
                        <?php endforeach; ?>
                    </div>
                </div> 
               </div>
            </div> 

            <!-- When other teammate want to modify please modify this part -->
<?php require __DIR__ . '/headandFoot/foot.php';?>