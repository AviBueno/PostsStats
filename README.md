# Posts Stats

Extract posts via API, process them and output statistics according to preliminary requirements.

## Setup

1. Clone the repository:\
	`git clone https://github.com/AviBueno/PostsStats.git`
1. Make sure [composer](https://getcomposer.org/download/) is installed and accessible from command line
1. Install dependencies:\
	`composer install`

## Code highlights

* app.php is the project's entry point.\
	It relies on several env. vars. which must either exist beforehand\
	or supplied via command line, e.g.:

		TOKEN_URL=[token url] \
		POSTS_URL=[posts url] \
		CLIENT_ID=[client id] \
		EMAIL=[email] \
		NAME=[name] \
		php src/app.php


	* The final output data is a pretty-printed JSON which is written to stdout.

* PostsFetcher handles HTTP requests and its main purpose is to return paginated posts data from the server.

* PostsStats is the main statistics class which will aggregate metrics based on month/week/user dimentions.

* MonthlyPostsStats is used for aggregating per-month statistics, and is part of PostsStats final report.

* Both PostsStats and MonthlyPostsStats contain an exportStats() method that will output the final report's data.
