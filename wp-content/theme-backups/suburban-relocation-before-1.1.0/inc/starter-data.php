<?php
/** Starter content data kept separate from importer logic. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function srs_starter_services() {
	return array(
		array(
			'title'      => 'Residential Moving',
			'slug'       => 'residential-moving',
			'kicker'     => 'Home moving specialists',
			'label'      => 'Homes & apartments',
			'excerpt'    => 'A complete plan for packing, protection, transportation and room-by-room delivery.',
			'image'      => 'residential-moving.webp',
			'sections'   => array(
				array( 'title' => 'A home move planned around real life', 'body' => array( 'A successful residential move connects packing, keys, closing dates, elevator reservations, building rules and access to the new property. We bring those details into one practical schedule before the crew arrives.', 'Your coordinator reviews the inventory, large or delicate items, stairs, parking and the distance between both addresses. That information determines the crew size, truck, protective materials and estimated time.' ) ),
				array( 'title' => 'Protection begins before loading', 'body' => array( 'Furniture is wrapped with moving blankets and stretch film where appropriate. Floors, railings and doorways can be protected in high-traffic areas, while hardware from disassembled pieces is kept organized for setup at delivery.', 'Boxes are grouped and labeled by room so they can be loaded securely and placed where they belong in the new home.' ), 'bullets' => array( 'Furniture wrapping', 'Floor and doorway protection', 'Disassembly and reassembly', 'Fragile-item handling', 'Room-by-room placement' ) ),
				array( 'title' => 'Choose the amount of packing help you need', 'body' => array( 'Full-service packing is useful when time is limited or an entire household must be prepared. Partial packing focuses the crew on kitchens, artwork, closets or other areas that need extra care.', 'If you prefer to pack yourself, ask about appropriate cartons and protective materials. Consistent box sizes and clear labels improve loading efficiency.' ), 'bullets' => array( 'Full household packing', 'Partial-room packing', 'Wardrobe cartons', 'Dish and glass protection', 'Unpacking assistance' ) ),
				array( 'title' => 'What happens on moving day', 'body' => array( 'The crew confirms the scope, walks through the property and prepares the loading path. Items are protected and loaded according to weight, size and delivery order.', 'At destination, the crew follows your room labels, places furniture and reassembles agreed pieces. A final walkthrough helps confirm that the truck and property have been checked.' ) ),
			),
		),
		array(
			'title'      => 'Local Moving',
			'slug'       => 'local-moving',
			'kicker'     => 'Washington, DC · Virginia · Maryland',
			'label'      => 'Local expertise',
			'excerpt'    => 'Careful, efficient moves across the DC metro area, Maryland and Virginia.',
			'image'      => 'residential-moving.webp',
			'sections'   => array(
				array( 'title' => 'A short distance still deserves a complete plan', 'body' => array( 'Local moves often happen on a tight schedule: one lease ends, keys become available and both properties may have limited parking or elevator windows. Planning those constraints in advance prevents avoidable waiting on moving day.', 'We review the inventory, property access, travel route and services you need, then recommend the right crew, truck and start time.' ) ),
				array( 'title' => 'Local knowledge keeps the day moving', 'body' => array( 'Moves throughout Washington, DC, Maryland and Virginia can involve loading zones, building certificates, elevator reservations, narrow streets and different parking rules. These details are part of the move plan, not last-minute surprises.', 'Share building instructions and access contacts early so the coordinator can prepare the crew and schedule.' ), 'bullets' => array( 'Apartment and condominium moves', 'Single-family homes', 'Senior relocations', 'Local office moves', 'Loading-only or delivery help' ) ),
				array( 'title' => 'Packing options for local moves', 'body' => array( 'Even when the destination is nearby, fragile items and furniture need the same protection used for a longer route. Full or partial packing can be added, or you can prepare boxes yourself before the crew arrives.', 'Clearly label the destination room on every carton and keep essential documents, medication and valuables with you.' ) ),
				array( 'title' => 'From arrival to final placement', 'body' => array( 'The crew begins with a walkthrough, protects the loading path and confirms special items. The truck is loaded for stability and an efficient unload at the new address.', 'At delivery, labeled boxes and furniture go directly to their assigned rooms. Agreed furniture is reassembled before the final walkthrough.' ) ),
			),
		),
		array(
			'title'      => 'Long-Distance Moving',
			'slug'       => 'long-distance-moving',
			'kicker'     => 'Interstate relocation',
			'label'      => 'Across the country',
			'excerpt'    => 'One coordinated plan from pickup to delivery, with clear updates along the way.',
			'image'      => 'long-distance-moving.webp',
			'sections'   => array(
				array( 'title' => 'A long-distance move starts with the inventory', 'body' => array( 'Distance changes how a move is packed, loaded and scheduled. A detailed inventory helps determine truck space, labor, packing requirements and equipment for oversized or delicate items.', 'Your coordinator also reviews pickup and delivery access, preferred dates and any items that should not travel on the moving truck.' ) ),
				array( 'title' => 'Packing designed for more miles', 'body' => array( 'Household goods on an interstate route spend more time in transit and must remain stable through loading, transportation and unloading. Furniture is wrapped, cartons are packed to prevent shifting and fragile items receive appropriate internal protection.', 'A clear labeling and inventory system makes it easier to check items at delivery and direct each carton to the correct room.' ), 'bullets' => array( 'Furniture protection', 'Fragile packing', 'Room and inventory labels', 'Oversized-item planning', 'Transport-ready loading' ) ),
				array( 'title' => 'Pickup, transit and delivery communication', 'body' => array( 'Your move plan identifies the pickup window, primary contact and delivery expectations. Coordinators, crews and drivers work from the same information so updates remain consistent.', 'Before delivery, access details and placement instructions are reconfirmed. If the new property is not ready, storage and a later delivery can be coordinated.' ) ),
				array( 'title' => 'Prepare both properties before the truck arrives', 'body' => array( 'Confirm parking, gates, elevator reservations and building requirements at origin and destination. Measure narrow stairways or doorways for unusually large furniture.', 'Keep identification, travel documents, medication, chargers and essential overnight items with you rather than in the shipment.' ) ),
			),
		),
		array(
			'title'      => 'Commercial Moving',
			'slug'       => 'commercial-moving',
			'kicker'     => 'Office and business relocation',
			'label'      => 'Business continuity',
			'excerpt'    => 'Structured office and business relocations designed to reduce downtime.',
			'image'      => 'commercial-moving.webp',
			'sections'   => array(
				array( 'title' => 'Business continuity shapes the moving plan', 'body' => array( 'A commercial relocation is successful when people, equipment and records arrive in the order the business needs them. We start with the operating schedule, building requirements, departmental priorities and the date each area must be functional.', 'The resulting plan identifies responsibilities, packing standards, move phases and a clear sequence for both locations.' ) ),
				array( 'title' => 'Plan department by department', 'body' => array( 'Workstations, common areas, storage rooms and specialized departments are grouped into manageable move zones. Labels connect each item to its destination floor, room or workstation so the delivery crew can place it correctly.', 'Phased moves can keep essential teams working while other areas are packed, transported and set up.' ), 'bullets' => array( 'Move-zone planning', 'Color-coded labels', 'Workstation mapping', 'Floor-by-floor sequencing', 'Employee move instructions' ) ),
				array( 'title' => 'Equipment, technology and records', 'body' => array( 'Monitors, desktop equipment, printers and other business assets require protective packing and clear ownership labels. Your internal IT team should define shutdown, data-security and reconnection responsibilities before moving begins.', 'Sensitive files and records can be handled under a documented process agreed during planning.' ) ),
				array( 'title' => 'Coordinate buildings as carefully as the inventory', 'body' => array( 'Commercial buildings may require certificates, loading-dock appointments, freight-elevator reservations and protective materials in common areas. We collect these rules before the move and incorporate them into the crew schedule.', 'Weekend, evening or phased scheduling can be considered when it reduces disruption to staff and customers.' ) ),
			),
		),
		array(
			'title'      => 'International Moving',
			'slug'       => 'international-moving',
			'kicker'     => 'Door-to-door coordination',
			'label'      => 'Global relocation',
			'excerpt'    => 'Packing, documentation, transport and destination support connected in one plan.',
			'image'      => 'long-distance-moving.webp',
			'sections'   => array(
				array( 'title' => 'Start international planning early', 'body' => array( 'An international relocation combines household moving with documentation, transport schedules, customs requirements and destination delivery. Starting early creates time to review what can be shipped, what should travel with you and which dates are realistic.', 'One move plan connects the origin crew, transportation arrangements and destination services so responsibilities remain clear.' ) ),
				array( 'title' => 'Inventory and documentation', 'body' => array( 'A detailed inventory supports packing, valuation and customs documentation. Requirements vary by destination, so your coordinator will identify the information needed for the route and explain when documents must be supplied.', 'Passports, visas, medical records and essential personal papers should remain with you rather than in the household shipment.' ) ),
				array( 'title' => 'Export-ready packing and protection', 'body' => array( 'Long transit times and multiple handling stages require consistent packing. Furniture, cartons and fragile items are prepared according to the shipment method and destination requirements.', 'Room labels and inventory references remain useful at destination, where the delivery team can place items and check the shipment against the documentation.' ), 'bullets' => array( 'Professional packing', 'Protective furniture wrapping', 'Detailed carton labeling', 'Fragile-item preparation', 'Inventory documentation' ) ),
				array( 'title' => 'Transportation and destination coordination', 'body' => array( 'The move schedule includes origin pickup, expected transport stages and the process for arranging final delivery. Destination access, building rules and placement requirements should be shared before the shipment arrives.', 'Temporary housing or an unfinished destination property can also be handled with storage and flexible delivery planning.' ) ),
			),
		),
	);
}

function srs_starter_primary_locations() {
	return array(
		array( 'title' => 'Maryland Movers', 'slug' => 'maryland', 'state' => 'Maryland', 'kicker' => 'Beltsville, Maryland', 'address' => '12000 Old Baltimore Pike, Beltsville, MD 20705', 'area' => 'Local Maryland, DC metro and long-distance routes', 'excerpt' => 'Residential, commercial, local and long-distance moving support throughout Maryland.' ),
		array( 'title' => 'Washington, DC Movers', 'slug' => 'washington-dc', 'state' => 'Washington, DC', 'kicker' => 'DC moving specialists', 'address' => 'Serving Washington, DC and the surrounding metro area', 'area' => 'Washington, DC and surrounding communities', 'excerpt' => 'Local and long-distance moving services for homes and businesses throughout Washington, DC.' ),
		array( 'title' => 'Virginia Movers', 'slug' => 'virginia', 'state' => 'Virginia', 'kicker' => 'Local and long-distance moving', 'address' => 'Serving Northern Virginia and statewide routes', 'area' => 'Northern Virginia and statewide routes', 'excerpt' => 'A practical moving plan and careful crew for relocations to, from and throughout Virginia.' ),
		array( 'title' => 'Colorado Movers', 'slug' => 'colorado', 'state' => 'Colorado', 'kicker' => 'Commerce City, Colorado', 'address' => '5651 E 56th Ave, Commerce City, CO 80022', 'area' => 'Denver metro and Colorado routes', 'excerpt' => 'Reliable residential and commercial relocation services from our Colorado moving team.' ),
		array( 'title' => 'California Movers', 'slug' => 'california', 'state' => 'California', 'kicker' => 'Tarzana, California', 'address' => '6034 Baird Ave, Tarzana, CA 91356', 'area' => 'Greater Los Angeles and California routes', 'excerpt' => 'Professional moving coordination for residential and commercial relocations in California.' ),
		array( 'title' => 'Texas Movers', 'slug' => 'texas', 'state' => 'Texas', 'kicker' => 'Lewisville, Texas', 'address' => '171 S Railroad St, Suite 5, Lewisville, TX 75057', 'area' => 'Dallas–Fort Worth and Texas routes', 'excerpt' => 'Residential and commercial moving support from our Lewisville service location.' ),
	);
}

function srs_starter_city_locations() {
	return array(
		array( 'applewood', 'Applewood Movers' ), array( 'arlington', 'Arlington Movers' ), array( 'ashburn', 'Ashburn Movers' ),
		array( 'aspen-park', 'Aspen Park Movers' ), array( 'aurora', 'Aurora Movers' ), array( 'beverly-hills', 'Beverly Hills Movers' ),
		array( 'boulder', 'Boulder Movers' ), array( 'brighton', 'Brighton Movers' ), array( 'broomfield', 'Broomfield Movers' ),
		array( 'castle-rock', 'Castle Rock Movers' ), array( 'centennial', 'Centennial Movers' ), array( 'centreville', 'Centreville Movers' ),
		array( 'chester', 'Chester Movers' ), array( 'chevy-chase', 'Chevy Chase Movers' ), array( 'college-park', 'College Park Movers' ),
		array( 'colorado-springs', 'Colorado Springs Movers' ), array( 'columbia-md', 'Columbia, MD Movers' ), array( 'commerce-city', 'Commerce City Movers' ),
		array( 'conifer', 'Conifer Movers' ), array( 'derwood', 'Derwood Movers' ), array( 'edgewater', 'Edgewater Movers' ),
		array( 'englewood', 'Englewood Movers' ), array( 'evergreen', 'Evergreen Movers' ), array( 'fairfax', 'Fairfax Movers' ),
		array( 'fort-collins', 'Fort Collins Movers' ), array( 'gaithersburg', 'Gaithersburg Movers' ), array( 'garrett-park', 'Garrett Park Movers' ),
		array( 'golden', 'Golden Movers' ), array( 'greeley', 'Greeley Movers' ), array( 'green-village', 'Green Village Movers' ),
		array( 'greenbelt', 'Greenbelt Movers' ), array( 'highlands-ranch', 'Highlands Ranch Movers' ), array( 'hollywood', 'Hollywood Movers' ),
		array( 'hyattsville', 'Hyattsville Movers' ), array( 'irvine', 'Irvine Movers' ), array( 'kensington', 'Kensington Movers' ),
		array( 'lafayette', 'Lafayette Movers' ), array( 'lakewood', 'Lakewood Movers' ), array( 'laurel-md', 'Laurel, MD Movers' ),
		array( 'laurel-va', 'Laurel, VA Movers' ), array( 'littleton', 'Littleton Movers' ), array( 'longmont', 'Longmont Movers' ),
		array( 'lorton', 'Lorton Movers' ), array( 'louisville', 'Louisville Movers' ), array( 'loveland', 'Loveland Movers' ),
		array( 'manassas', 'Manassas Movers' ), array( 'mclean', 'McLean Movers' ), array( 'montgomery', 'Montgomery Movers' ),
		array( 'monument', 'Monument Movers' ), array( 'newport-news', 'Newport News Movers' ), array( 'norfolk', 'Norfolk Movers' ),
		array( 'northglenn-henderson', 'Northglenn & Henderson Movers' ), array( 'olney', 'Olney Movers' ), array( 'parker', 'Parker Movers' ),
		array( 'potomac', 'Potomac Movers' ), array( 'rockville', 'Rockville Movers' ), array( 'sandy-spring', 'Sandy Spring Movers' ),
		array( 'silver-spring', 'Silver Spring Movers' ), array( 'springfield', 'Springfield Movers' ), array( 'sterling', 'Sterling Movers' ),
		array( 'thornton', 'Thornton Movers' ), array( 'towson', 'Towson Movers' ), array( 'upper-marlboro', 'Upper Marlboro Movers' ),
		array( 'vienna', 'Vienna Movers' ), array( 'wellington', 'Wellington Movers' ), array( 'westminster', 'Westminster Movers' ),
		array( 'woodbridge', 'Woodbridge Movers' ), array( 'woodland-park', 'Woodland Park Movers' ), array( 'woodmoor', 'Woodmoor Movers' ),
		array( 'bethesda', 'Bethesda Movers' ), array( 'baltimore', 'Baltimore Movers' ), array( 'annapolis', 'Annapolis Movers' ),
		array( 'denver', 'Denver Movers' ), array( 'los-angeles', 'Los Angeles Movers' ), array( 'alexandria', 'Alexandria Movers' ),
	);
}
