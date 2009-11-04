<?php
/**
 * IH Simple Commerce Purchases Plugin
 *
 * @package     SimpleCommercePurchases
 * @version     1.0.2
 * @author      Mike Kroll <http://imagehat.com>
 * @copyright   Copyright (c) 2008-2009 Mike Kroll
 * @license     http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-Share Alike 3.0 Unported
 **/

$plugin_info = array(
                 'pi_name'          => 'Simple Commerce Purchases',
                 'pi_version'       => '1.0.2',
                 'pi_author'        => 'Mike Kroll',
                 'pi_author_url'    => 'http://imagehat.com/',
                 'pi_description'   => 'Display Simple Commerce purchases.',
                 'pi_usage'         => Ih_simple_commerce_purchases::usage()
               );

class Ih_simple_commerce_purchases
{
    var $return_data;
    
    /**
     * Constructor
     */    
    function Ih_simple_commerce_purchases()
    {
        global $TMPL, $SESS, $DB, $IN, $FNS, $LOC;

				$out = '';
	
				// Parameters
				$member_id = ($TMPL->fetch_param('member_id')) ? $TMPL->fetch_param('member_id') : $SESS->userdata('member_id');
				$weblog_id = ($TMPL->fetch_param('weblog_id')) ? $TMPL->fetch_param('weblog_id') : FALSE;
				$orderby = ($TMPL->fetch_param('orderby')) ? $TMPL->fetch_param('orderby') : 'date';
				$sort = ($TMPL->fetch_param('sort') == 'asc') ? 'asc' : 'desc';
				$limit = ($TMPL->fetch_param('limit') && is_numeric($TMPL->fetch_param('limit'))) ? $TMPL->fetch_param('limit') : 500;
				$entry_id = ($TMPL->fetch_param('weblog_entry_id')) ? $TMPL->fetch_param('weblog_entry_id') : FALSE;
				
				
				// User Valid?
				$query = $DB->query("SELECT screen_name FROM exp_members WHERE member_id = '".$DB->escape_str($member_id)."'");
    							 
                if ($query->num_rows == 0) 
				{
					return FALSE;
				}

				// Gather Purchases
				$sql = "SELECT wt.title AS item_purchased, wt.entry_id AS weblog_entry_id,
								m.screen_name AS purchaser,
								scp.member_id, scp.purchase_id, scp.txn_id AS transaction_id, scp.item_id, scp.purchase_date, scp.item_cost
								FROM exp_simple_commerce_purchases scp, exp_simple_commerce_items sci, exp_members m, exp_weblog_titles wt
								WHERE scp.item_id = sci.item_id
								AND sci.entry_id = wt.entry_id
								AND scp.member_id = m.member_id
								AND scp.member_id = '".$DB->escape_str($member_id)."'";

				if ($TMPL->fetch_param('weblog_id') !== FALSE)
				{
					$sql .= $FNS->sql_andor_string($weblog_id, 'wt.weblog_id');
				}
				if ($TMPL->fetch_param('weblog_entry_id') !== FALSE)
				{
					$sql .= $FNS->sql_andor_string($entry_id, 'wt.entry_id');
				}
				$sql .= ("orderby" == "item") ? " ORDER BY scp.item_id" : " ORDER BY scp.purchase_date";
				$sql .= " ".$sort;
				$sql .= " LIMIT ".$limit;
				
				$query = $DB->query($sql);
				
				if ( $query->num_rows == 0 )
				{
					return $this->return_data = $TMPL->no_results();
				}
				
				// Loop through purchases	
				$count = 0;
				foreach($query->result as $row)
				{			
					$tagdata = $TMPL->tagdata;
					
					// Parse Tag Data
					foreach($row as $key => $value)
					{
						// Single Variables
						$tagdata = str_replace(LD.$key.RD, $value, $tagdata);
						
						// Time Format
						if ($key == 'purchase_date')
						{
							if (preg_match("/".LD.$key."\s+format=[\"'](.*?)[\"']".RD."/s", $tagdata, $match))
							{
								$str	= $match['1'];

								$codes	= $LOC->fetch_date_params( $match['1'] );

								foreach ( $codes as $code )
								{
									$str	= str_replace( $code, $LOC->convert_timestamp( $code, $value, TRUE ), $str );
								}

								$tagdata	= str_replace( $match['0'], $str, $tagdata );
							}
						}
					}
					
					// Count
					$count++;			
					$tagdata = str_replace(LD.'count'.RD, $count, $tagdata);
					
					// Conditionals
					$tagdata = $FNS->prep_conditionals($tagdata, array('count' => $count));
					$tagdata = $FNS->prep_conditionals($tagdata, $row);
					
					$out .= $tagdata;
					
				}
				
				$tagdata = $out;
				
				// Total Results
				$tagdata = str_replace(LD.'total_results'.RD, $count, $tagdata);
				$tagdata = $FNS->prep_conditionals($tagdata, array('total_results' => $count));
								
				$this->return_data = $tagdata;
    }
    
    /**
     * Usage
     */
    function usage()
    {
        ob_start(); 
?>

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
weblog_entry_id: Restrict purchases to only a particular weblog entry.
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


<?php
        $buffer = ob_get_contents();
        ob_end_clean(); 

        return $buffer;
    }
    
}

/* End of file ext.ih_simple_commerce_purchases.php */
/* Location: ./system/extensions/ext.ih_simple_commerce_purchases.php */