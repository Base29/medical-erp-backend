<?php

return [
    // Status codes for Offers
    'OFFER' => [
        'DECLINED' => 0,
        'ACCEPTED' => 1,
        'MADE' => 2,
        'REVISED' => 3,
        'PENDING' => 4,
        'DISCARDED' => 5,
        'AMENDMENT' => [
            'DECLINED' => 0,
            'ACCEPTED' => 1,
            'NEGOTIATING' => 2,
            // Status codes for offer amendment life cycle (boolean)
            'ACTIVE' => 1,
            'INACTIVE' => 0,
        ],
        // Status codes for life cycle of the offer (boolean)
        'ACTIVE' => 1,
        'INACTIVE' => 0,
    ],
    // Status codes for Applicants in the interview process
    'APPLICANT_STATUS' => [
        'REJECTED' => 0,
        'ACCEPTED' => 1,
        'REFERRED' => 2,
    ],
];