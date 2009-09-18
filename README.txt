Allows you to list information for purchases made through the
Simple Commerce Module for any individual member.

---------------------------
Tag Example:
---------------------------
	
{exp:ih_simple_commerce_purchases member_id="{segment_3}"}
	<p>{item_purchased} - {purchase_date format="%F %d, %Y"}</p>
{/exp:ih_simple_commerce_purchases}

---------------------------
Available parameters:
---------------------------

member_id: Member ID to display purchases for. If no member id is set will default to current logged in member.
weblog_id: Restrict purchases to only a particular weblog.
orderby: 'item' or 'date'. Defaults to 'date'.
sort: 'asc' or 'desc'. Defaults to 'desc'.
limit: Limit number of results

---------------------------
Available variables:
---------------------------

{item_purchased}
{weblog_entry_id}
{item_id}
{item_cost}
{purchase_id}
{transaction_id}
{purchase_date format="%F %d, %Y"}
{member_id}
{purchaser}

{count}
{total_results}

---------------------------
Retrieving weblog data:
---------------------------

You can wrap the standard weblog tags within this plugin to retrieve all of the associated entry data by using the parse parameter.
(See "Changing Parsing Order": http://expressionengine.com/docs/templates/plugins.html)

{exp:ih_simple_commerce_purchases member_id="{segment_3}" parse="inward"}

    {exp:weblog:entries weblog="my_weblog" entry_id="{weblog_entry_id}"}
	    
	    <h1>{title}</h1>
	    {my_custom_field}
	    {another_custom_field}
	    <p>{item_purchased} - {purchase_date format="%F %d, %Y"}</p>
	    
	{/exp:weblog:entries}
	
{/exp:ih_simple_commerce_purchases}


---------------------------
Change Log
---------------------------

1.0.1 - Fixed php error, fixed typos in documentation and updated.
1.0.0 - Initial release.