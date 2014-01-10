<?php
function outputFeaturesList( $data, $level=1)
{
	
	$groupClass = '';
	
	if($level == 1 || $level == 2 )
	{
		$groupClass = 'group';
	}
	
	$html = '';
	
	foreach( $data AS $key => $feature)
	{
		$html .= '<div class="' . $groupClass . '">';
		$html .= '<div class="' . $feature['type']  . ' level level' . $level . '">' . $feature['title'] . '</div>';
		
		if(isset($feature['children']) && !empty($feature['children']))
		{
			$html .= outputFeaturesList( $feature['children'], $level+1 );
		}
		$html .= '</div>';
	}
	
	return $html;
	
}

function outputFeatures( $quote)
{
	$html = '';
	
	if(is_array($quote['features']) && !empty($quote['features']))
	{
		$html .= outputFeaturesList( $quote['features'] );
	}
	
	$showExtra = false;
	
	$extra = '';
	if(isset($quote['includeConcept']) && $quote['includeConcept'] == 1 )
	{
		$showExtra = true;
		$extra .= '<div class="level3 feature">Conceptual Development</div>';
	}
	
	if(isset($quote['includeConfig']) && $quote['includeConfig'] == 1 )
	{
		$showExtra = true;
		$extra .= '<div class="level3 feature">System Configuration</div>';
	}
	
	if(isset($quote['includeDeploy']) && $quote['includeDeploy'] == 1 )
	{
		$showExtra = true;
		$extra .= '<div class="level3 feature">System Deployment</div>';
	}
	
	if(isset($quote['includeEnv']) && $quote['includeEnv'] == 1 )
	{
		$showExtra = true;
		$extra .= '<div class="level3 feature">Test/Production Setup</div>';
	}
	
	if(isset($quote['includePM']) && $quote['includePM'] == 1 )
	{
		$showExtra = true;
		$extra .= '<div class="level3 feature">Project Management</div>';
	}
	
	if(isset($quote['includeTesting']) && $quote['includeTesting'] == 1 )
	{
		$showExtra = true;
		$extra .= '<div class="level3 feature">Testing &amp; Quality Assurance</div>';
	}
	
	if(isset($quote['includeTraining']) && $quote['includeTraining'] == 1 )
	{
		$showExtra = true;
		$extra .= '<div class="level3 feature">Training &amp Documentation</div>';
	}

	if($showExtra)
	{
		$html .= '<div class="group">';
		$html .= '<div class="major level1">Additional</div>';
			$html .= '<div class="group">';
				$html .= '<div class="minor level2">Infrastructure</div>';
				$html .= $extra;
			$html .= '</div>';
		$html .= '</div>';
	}
	
	return $html;
}


function outputHTML($data)
{
	
	global $today;
	global $todaySigned;
	
	$approvalURL = 'http://localhost:3000/quote/response/' . $data['approvalCode'];
	
	$client = $data['client'];
	$quote = $data['quote'];
	
	$html = '';
	$html .= '<html>';
	$html .= '<head></head>';
	$html .= '<body>';
	
	if(strlen($data['coverletter']))
	{
		$html .= '<p>' . nl2br( $data['coverletter'] ) . '</p>';
		$html .= '<p>To approve, request a re-quote, or decline this quote please click the link below:</p>';
		$html .= '<p><a href="' . $approvalURL . '">' . $approvalURL . '</a></p>';
		
		$html .= '<pagebreak />';
	}
	
	$html .= '<h1 style="padding:0; margin:0;">' . $client['name'] . '</h1>';
	$html .= '<h2 style="padding:0; margin:0;">' . $quote['title'] . '</h2>';
	
	$html .= '<p><strong>Executive Summary</strong><br/>';
	$html .= $quote['description'];
	$html .= '</p>';
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">';
	$html .= '<tr>';
	$html .= '<td width="50%" align="top" style="border-bottom:1px solid #000;"><strong>Total:</strong></td>';
	$html .= '<td width="50%" align="right" valign="top" style="border-bottom:1px solid #000;"><strong>' . money_format('%n', $quote['estTotal'])  . '</strong></td>';
	$html .= '</tr>';
	$html .= '</table>';
	$html .= '</div>';
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;"><strong>AGREEMENT</strong></p>';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;">This agreement constitutes the sole agreement between the contractor and client regarding the above referenced project. Any additional work not specified in this contract or any other amendment or modification to this contract must be authorized by a written request signed by both client and the contractor. The undersigned hereby agree to the terms, conditions and stipulations of this agreement on behalf of his or her organization or business. This Agreement constitutes the entire understanding of the parties. Any changes or modifications thereto must be in writing and signed by both parties.</p>';
	$html .= '</div>';
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;"><strong>STATEMENT OF GUARANTEE</strong></p>';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;">We the undersigned guarantee that the project as it is defined within the proposal shall be completed within the time as is defined witin this proposal and as is within the contractors control. The guarantee is upon condition that client is able to uphold all responsibilities, deliveries and milestones as they are defined and agreed to upon the start of the project. The project cost as it is defined with in this document is also guaranteed by contractor. Assumptions not defined at the project start, and/or additional out-of-scope requests not defined at the project start are subject to additional costs and time extensions. All new features will be documented in change-orders and are subject to approval by client before implementation.</p>';
	$html .= '</div>';
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
	$html .= '<tr>';
	$html .= '<td width="30" valign="bottom" style=""><strong>By</strong></td>';
	$html .= '<td align="top" style="border-bottom:1px solid #000;">';
	$html .= '<img src="signature.png" />';
	$html .= '</td>';
	$html .= '<td width="50" valign="bottom" style=""><strong>Date:</strong></td>';
	$html .= '<td width="100" align="right" valign="bottom" align="center" style="border-bottom:1px solid #000;">' . $todaySigned . '</strong></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td width="30" align="top" style="">&nbsp;</td>';
	$html .= '<td align="top" style="">';
	$html .= 'Chloe Jones, ';
	$html .= 'FreshJones, LLC (Contractor)<br/>';
	//$html .= '14 Miner Street, Greenfield, MA 01301&nbsp;&nbsp;';
	$html .= 'Phone: (413) 475-1810&nbsp;&nbsp;E-mail: chloe@freshjones.com';
	$html .= '</td>';
	$html .= '<td width="50" align="top" style="">&nbsp;</td>';
	$html .= '<td width="100" align="right" valign="top" style="">&nbsp;</strong></td>';
	$html .= '</tr>';
	$html .= '</table>';
	$html .= '</div>';
	
	$html .= '<div style="padding-top:30px; padding-bottom:10px;">';
	$html .= '<p style="font-size:9pt; padding-bottom:30px; margin:0;"><strong>AGREED TO BY:</strong></p>';
	$html .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
	$html .= '<tr>';
	$html .= '<td width="30" valign="bottom" style=""><strong>By</strong></td>';
	$html .= '<td align="top" style="border-bottom:1px solid #000;">&nbsp;</td>';
	$html .= '<td width="50" valign="bottom" style=""><strong>Date:</strong></td>';
	$html .= '<td width="100" align="right" valign="bottom" align="center" style="border-bottom:1px solid #000;">&nbsp;</strong></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td width="30" align="top" style="">&nbsp;</td>';
	$html .= '<td align="top" style="">';
	$html .= 'Authorized Representative, ';
	$html .= $client['name'] . ' (Client)<br/>';
	//$html .= '14 Miner Street, Greenfield, MA 01301&nbsp;&nbsp;';
	//$html .= 'Phone: (413) 475-1810&nbsp;&nbsp;E-mail: chloe@freshjones.com';
	$html .= '</td>';
	$html .= '<td width="50" align="top" style="">&nbsp;</td>';
	$html .= '<td width="100" align="right" valign="top" style="">&nbsp;</strong></td>';
	$html .= '</tr>';
	$html .= '</table>';
	$html .= '</div>';
	
	$html .= '<pagebreak />';
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;"><strong>COMPANY / ORGANIZATION</strong></p>';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;">' . $client['name'] . '</p>';
	$html .= '</div>';
	
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;"><strong>PROJECT TITLE</strong></p>';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;">' . $quote['title'] . '</p>';
	$html .= '</div>';
	
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;"><strong>PROJECT OVERVIEW</strong></p>';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;">' . $quote['description'] . '</p>';
	$html .= '</div>';
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;"><strong>PROJECT PURPOSE</strong></p>';
	$html .= '<p style="font-size:9pt; padding:0; margin:0;">' . $quote['purpose'] . '</p>';
	$html .= '</div>';
	
	$html .= '<div style="padding-top:10px; padding-bottom:10px;">';
	$html .= '<p style="font-size:9pt; margin-top:0; margin-bottom:5px;"><strong>PROJECT SCOPE</strong></p>';
	
	$html .= '<div class="vertical-padding">';
	
	$html .= '<div class="halfcol">';
	
	$included = explode("\n", $quote['scope']['included']);
	$numIncluded = count($included);
	
	$html .= '<div class="table-headers">WHATS INCLUDED</div>';
	
	$counter=0;
	foreach($included AS $eachItem)
	{
		$class = 'ruledlist';
	
		if($counter == $numIncluded-1)
		{
			$class = 'ruledlist last';
		}
		$html .= '<div class="' . $class. '">' . $eachItem . '</div>';
		$counter++;
	}
	
	$html .= '</div>';
	
	$html .= '<div class="halfcol">';
	
	$notincluded = explode("\n", $quote['scope']['notincluded']);
	$numNotIncluded = count($notincluded);
	
	$html .= '<div class="table-headers">WHATS NOT INCLUDED</div>';
	
	$counter=0;
	foreach($notincluded AS $eachItem)
	{
		$class = 'ruledlist';
	
		if($counter == $numNotIncluded-1)
		{
			$class = 'ruledlist last';
		}
	
		$html .= '<div class="' . $class . '">' . $eachItem . '</div>';
		$counter++;
	}
	
	$html .= '</div>';
	
	$html .= '</div>';
	
	$html .= '<div class="fullcol">';
	
	$assumptions = explode("\n", $quote['assumptions']);
	$numAssumptions = count($assumptions);
	
	$html .= '<div class="table-headers">ASSUMPTIONS</div>';
	
	$counter=0;
	foreach($assumptions AS $eachItem)
	{
		$class = 'ruledlist';
	
		if($counter == $numAssumptions-1)
		{
			$class = 'ruledlist last';
		}
	
		$html .= '<div class="' . $class . '">' . $eachItem . '</div>';
		$counter++;
	}
	
	$html .= '</div>';
	
	$html .= '</div>';
	
	$html .= '<pagebreak />';
	
	$html .= '<h2 style="padding:0; margin-top:0; margin-bottom:20px;">Feature List</h2>';
	
	$html .= outputFeatures( $quote );

	$html .= '</body>';
	$html .= '</html>';
	
	return $html;
}
