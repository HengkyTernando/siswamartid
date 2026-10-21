# Review profanity dictionary

`badwords-list.json` is the English and Indonesian word data from the MIT-licensed
[`soluvas/badwords-list`](https://github.com/soluvas/badwords-list) project. The
upstream license is included as `LICENSE-badwords-list.txt`.

The application uses this data on the server when a review is submitted. The
upstream lists are community-maintained and may miss spelling variants or flag
some words out of context; the review form shows a correction message whenever
the filter matches.
