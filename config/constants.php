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
    // Statuses for hiring requests
    'HIRING_REQUEST' => [
        'PENDING' => 'pending',
        'APPROVED' => 'approved',
        'DECLINED' => 'declined',
        'ESCALATED' => 'escalated',
        'CONTRACT_TYPE' => [
            'PERMANENT' => 'permanent',
            'FIXED_TERM' => 'fixed-term',
            'CASUAL' => 'casual',
            'ZERO_HOUR' => 'zero-hour',
        ],
    ],
    // Statuses for User
    'USER' => [
        'ACTIVE' => 1,
        'INACTIVE' => 0,
        'HIRED' => 1,
        'NOT_HIRED' => 0,
        'CANDIDATE' => 1,
        'NOT_CANDIDATE' => 0,
        'LOCUM' => 1,
        'NOT_LOCUM' => 0,
        'BLACKLISTED' => 1,
        'NOT_BLACKLISTED' => 0,
        'APPLICANT_STATUS' => [
            'REJECTED' => 0,
            'ACCEPTED' => 1,
            'REFERRED' => 2,
        ],
    ],
    // Constants for interview
    'INTERVIEW' => [
        'TYPE' => [
            'FIRST_INTERVIEW' => 'first-interview',
            'SECOND_INTERVIEW' => 'second-interview',
        ],
    ],

    // Constants for Induction
    'INDUCTION' => [
        'COMPLETED' => 1,
        'INCOMPLETE' => 0,
    ],

    // Constants for Appraisal
    'APPRAISAL' => [
        'COMPLETED' => 1,
        'INCOMPLETE' => 0,
        'APPROVED' => 1,
        'DECLINED' => 0,
    ],
];