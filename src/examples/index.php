<?php
// Time
$start_time = microtime(true);
// Include the TourCMS API wrapper
include 'config.php';

// Pagination
$per_page = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Location from the form
$location = isset($_GET['location']) ? $_GET['location'] : '';

$parameters = array(
	"per_page" => $per_page,
	"page" => $page,
	"location" => $location,
	"product_type" => 4, //(No overnight stay) (product_type 4) in Spain (country ES).
	"country" => "ES"
);

$querystring = http_build_query($parameters);
$result = $tc->search_tours($querystring, $channel_id);

$end_time = microtime(true);
$execution_time = ($end_time - $start_time);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Featured Tours : TourCMS API</title>
		<link rel="stylesheet" href="css/normalize.css" />
		<link rel="stylesheet" href="css/examples.css" />
		<style>
			.tour-image {
				max-width: 400px;
				max-height: 400px;
				width: auto;
				height: auto;
				border-radius: 6px;
				display: block;
				margin: 10px auto;
			}
			.tour-unit {
				border: 1px solid #ccc;
				border-radius: 6px;
				padding: 10px;
				margin: 10px 0;
			}
			details summary {
				cursor: pointer;
				color: blue;
				text-decoration: underline;
				display: inline-block;
			}

			details summary:hover {
				color: darkblue;
			}
		</style>
	</head>
	<body>
		<details>
			<p class="execution-time">API Response Time: <?php echo number_format($execution_time, 4); ?> seconds</p>
			<summary><h2>API Examples</h2></summary>
			<h4>For all account types</h4>
			<ul>
				<li>
					<a href="anyone/api_rate_limit_status/">Check current API rate limit status</a>
					<span class="ancillary">
						<a href="http://www.tourcms.com/support/api/mp/rate_limit_status.php" target="_blank">API rate limit status</a>
					</span>
				</li>
				<li>
					<a href="anyone/search_tours/">Display a list of Tours, link through to supplier site for full details</a>
					<span class="ancillary">
						<a href="http://www.tourcms.com/support/api/mp/tour_search.php" target="_blank">Search Tours</a>
					</span>
				</li>
				<li>
					<a href="anyone/search_and_show_tours/">Display a list of Tours, stay on site when viewing tour details</a>
					<span class="ancillary">
						<a href="http://www.tourcms.com/support/api/mp/tour_search.php" target="_blank">Search Tours</a>
							<a href="http://www.tourcms.com/support/api/mp/tour_search.php" target="_blank">Show Tour</a>
					</span>
				</li>
				<li>
					<a href="anyone/show_tour_map/">Generate a Google Map of a Tour start location</a>
					<span class="ancillary">
							<a href="http://www.tourcms.com/support/api/mp/tour_search.php" target="_blank">Show Tour</a>
					</span>
				</li>
				<!--li>
					<a href="anyone/search_tours_paged/">Display a list of Tours/Hotels with pagination</a>
					<span class="ancillary">
						<a href="http://www.tourcms.com/support/api/mp/search_tours.php">Search Tours/Hotels</a>
					</span>
				</li-->
			</ul>
			<p>More examples in future versions of the wrapper, for now check out the <a href="http://www.tourcms.com/support/api/mp/examples.php" target="_blank">examples page on our website</a> and don't forget each of the methods in <a href="http://www.tourcms.com/support/api/mp/" target="_blank">our documentation</a> contains sample code.</p>

			<h4>For Operator/Supplier accounts only</h4>
			<h4>For Partner/Affiliate accounts only</h4>
			<h3>Problems?</h3>
			<ul>
				<li>Run the <a href="anyone/test_environment/">Environment Test</a></li>
				<li>Ask in the <a href="http://community.tourcms.com/" target="_blank">forums</a> or <a href="http://www.tourcms.com/company/contact.php" target="_blank">contact us</a> - we're happy to help ☺</li>
				<li>Don't forget <a href="http://www.tourcms.com/support/api/mp/examples.php">more feature complete examples</a> can be found on our website.</li>
			</ul>
		</details>

		<h2>Featured Tours</h2>
    <section>
        <div class="tour">
            <form action="" method="GET">
                <label for="location">Enter the location:</label>
                <input type="text" id="location" name="location" placeholder="Example: Barcelona" value="<?php echo htmlspecialchars($location); ?>" required>
                <button type="submit">Search</button>
            </form>

            <?php
            // Check if the result is ok
            if ($result->error == "OK") {
                // Calculate how many pages we have
                $pages = ceil($result->total_tour_count / $per_page);

                echo "<p>Page <strong>$page</strong> of <strong>$pages</strong></p>";

                // Loop through and display the tours
                foreach ($result->tour as $tour) :
                    // Only display the tour if the location matches the search input
                    if (empty($location) || stripos($tour->location, $location) !== false) :
            ?>
                        <div class="tour-unit">
                            <h4>
                                <a href="<?php print $tour->tour_url; ?>">
                                    <?php print $tour->tour_name; ?>
                                </a>
                            </h4>

                            <?php if (!empty($tour->image)): ?>
                                <img src="<?php print $tour->image; ?>" alt="<?php print htmlspecialchars($tour->tour_name); ?>" class="tour-image" />
                            <?php endif; ?>

                            <p class="summary"><?php print $tour->summary; ?></p>

                            <p><?php print $tour->shortdesc; ?></p>
                            <p>Duration: <?php print $tour->duration; ?></p>
                            <p>Price: <?php print $tour->from_price_display; ?></p>
                            <p>Start time: <?php print $tour->start_time; ?></p>
                            <p>End time: <?php print $tour->end_time; ?></p>
                            <p>Location: <?php print $tour->location; ?></p>
                            <p>Languages: <?php print !empty($tour->languages_spoken) ? $tour->languages_spoken : "Not specified"; ?></p>
                            <p class="buttons">
                                <a href="<?php print $tour->tour_url; ?>">View full details</a>
                                <a href="<?php print $tour->book_url; ?>">Book online</a>
                            </p>
                        </div>
                    <?php
                    endif; // End if location matches
                endforeach;
            }

            // Basic pagination
            if ($page > 1) {
                print '<a href="?page=1' . ($location ? '&location=' . urlencode($location) : '') . '">&lt;&lt; First page</a>';
                print '<a href="?page=' . ($page - 1) . ($location ? '&location=' . urlencode($location) : '') . '">&lt; Previous page</a>';
            }
            if ($page < $pages) {
                print '<a href="?page=' . ($page + 1) . ($location ? '&location=' . urlencode($location) : '') . '">Next page &gt;</a>';
                print '<a href="?page=' . $pages . ($location ? '&location=' . urlencode($location) : '') . '">Last page &gt;&gt;</a>';
            }
            ?>
        </div>
    </section>

	</body>
</html>
