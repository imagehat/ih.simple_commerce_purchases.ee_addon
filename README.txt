Allows you to list information for purchases made through the
Simple Commerce Module for any individual member.

---------------------------
Tag Example:
---------------------------
	
{exp:ih_simple_commerce_purchases member_id="{segment_3}"}
	<p>{item_purchased} - {purchase_date format="%F %d, %Y"}</p>
{exp:ih_simple_commerce_purchases}

---------------------------
Available parameters:
---------------------------

member_id: Member ID to display purchases for. If no member id is set will default to current logged in member.
orderby: 'item' or 'date'. Defaults to 'date'.
sort: 'asc' or 'desc'. Defaults to 'desc'.

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
Change Log
---------------------------

1.0.0 - Initial Release