<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function App\Core\System\utils\env;

/**
 * EmailTemplateController handles the email templates.
 */
class EmailTemplateController extends Controller
{

    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        parent::__construct($siteRepository);
    }

    // 1. opt send email verification
    public function sendEmailVerification(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            $context = [
                'subject' => 'OTP Verification',
                'client_name' => '',
                'otp_code' => '',
                'otp_expiry_time' => '',
            ];

            $html = $twig->render('otp-verification.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function physicalTour(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            // $context = [
            //     'subject' => 'Confirmed: Your Showroom Tour with Krost',
            //     'client_name'    => 'John Doe',
            //     'otp'            => implode(' ', str_split('123456')), // 1 2 3 4 5 6
            //     'expiry_minutes' => 10,
            // ];

            // $html = $twig->render('physical-tour.html.twig', $context);

            // $guestContext = [
            //     'subject'           => 'Confirmed: Your Showroom Tour with Krost',
            //     'client_name'       => 'John Doe',
            //     'client_email'      => 'john.doe@example.com',
            //     'meeting_date'      => date('d/m/Y'), // format: d/m/Y
            //     'time_zone'         => 'Australia/Sydney',
            //     'date'             =>  date('d/m/Y'),
            //     'location'          => '123 Main St, Anytown, USA',
            //     'platform_url'      => 'https://www.google.com',
            //     'platform_label'    => 'Online Link',
            //     'reschedule_url'    => 'https://www.google.com',
            //     'cancel_url'        => 'https://www.google.com',
            //     'salesperson_name'  => 'The Krost Team',
            //     'google_map_link' => 'https://www.google.com',
            //     'company_name' => 'Krost Business Furniture',
            // ];



            $appUrlGuest = rtrim((string) env('APP_URL'), '/');

            $guestContext = [
                'subject' => 'Project saved - Krost',
                'project_title' => 'Executive Office, Level 14',
                'project_name' => 'Executive Office, Level 14',
                'saved_at' => date('j M Y, g:i A T'),
                'items_count' => 6,
                'items' => [
                    [
                        'title' => 'Acoustic Pod - Quad',
                        'description' => 'Storm Grey - 4-seater',
                        'image_url' => null,
                    ],
                    [
                        'title' => 'Lounge Bench - Modular',
                        'description' => 'Sage upholstery - 1800mm',
                        'image_url' => null,
                    ],
                    [
                        'title' => 'Side Table - Round',
                        'description' => 'White ceramic - 500',
                        'image_url' => null,
                    ],
                    [
                        'title' => 'Task Chair - Linear',
                        'description' => 'Charcoal mesh - Aluminium base',
                        'image_url' => null,
                    ],
                    [
                        'title' => 'Pendant - Halo 600',
                        'description' => 'Brushed brass - Dimmable',
                        'image_url' => null,
                    ],
                    [
                        'title' => 'Rug - Boucle Weave',
                        'description' => 'Bone - 2400 x 3000',
                        'image_url' => null,
                    ],
                ],
                'pinboard_url' => 'https://www.google.com',
                'share_url' => 'https://www.google.com',
                'next_steps_intro' => 'Ready to see these pieces in person? Book a showroom tour or request a consultation directly through your dashboard.',
                'showroom_tour_url' => 'https://www.google.com',
                'consultation_url' => 'https://www.google.com',
                'showroom_locations_text' => 'Sydney - Melbourne - Brisbane',
                'consultation_subtext' => 'Spec advice from our specialists',
                'sender_name' => 'Krost Sales Team',
                'submission_date' => date('d/m/Y'),
                'icon_chat_src_briefcase' => $appUrlGuest . '/media/design-resource/icons/briefcase.png',
                'icon_chat_src_message' => $appUrlGuest . '/media/design-resource/icons/message.png',
            ];

             $html = $twig->render('account-service-request.html.twig', $guestContext);
            //  $html = $twig->render('booking-admin-physical-tour.html.twig', $guestContext);
            //  $html = $twig->render('contact-us-touch.html.twig', $guestContext);
            

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function consultationTomorrow(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'Our Online Consultation is Tomorrow',
                'recipient_name' => '',
                'meeting_date' => '',
                'meeting_time' => '',
                'meeting_url' => '#',
                'meeting_link_label' => '',
                'consultant_name' => '',
                'reschedule_url' => '#',
                'reschedule_label' => '',
                'salesperson_name' => '',
            ];

            $html = $twig->render('consultation-tomorrow.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showroomTomorrow(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'Your Showroom Tour is Tomorrow',
                'recipient_name' => '',
                'meeting_date' => '',
                'meeting_time' => '',
                'location_url' => '#',
                'location_label' => '',
                'consultant_name' => '',
                'reschedule_url' => '#',
                'reschedule_label' => '',
                'salesperson_name' => '',
            ];

            $html = $twig->render('showroom-tomorrow.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function onlineMeeting(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'Internal Notification - New Online Meeting Booking',
                'recipient_name' => '',
                'meeting_date' => '',
                'meeting_time' => '',
                'platform_url' => '#',
                'platform_label' => '',
                'client_email' => '',
            ];

            $html = $twig->render('online-meeting.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function physicalShowroomTour(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'Internal Notification - New Showroom Booking',
                'recipient_name' => '',
                'meeting_date' => '',
                'meeting_time' => '',
                'location' => '',
                'client_email' => '',
            ];

            $html = $twig->render('physical-showroom-tour.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function virtualMeeting(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'Confirmed: Your Online Consultation with Krost',
                'client_name' => 'anamul',
                'meeting_date' => '',
                'meeting_time' => '',
                'platform_url' => '#',
                'platform_label' => '',
                'reschedule_url' => '#',
                'salesperson_name' => '',
            ];

            $html = $twig->render('online-meeting.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    public function clientAfterSubmission(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => "We've received your project! – Demo Project",
                'client_name' => 'Nazmul',
                'project_name' => 'Demo Project',
                'items_saved' => '10',
                'submission_date' => '2026-04-12',
                'collections_url' => '#',
            ];

            $html = $twig->render('client-after-submission.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    public function pinboardSubmission(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'PINBOARD SUBMISSION - [Project Name] from [Client Name]',
                'team_name' => '',
                'client_name' => '',
                'client_email' => '',
                'company' => '',
                'phone' => '',
                'pinboard_name' => '',
                'items_count' => '',
                'board_url' => '#',
                'board_link_label' => '',
                'pinboard_phone' => '',
                'client_notes' => '',
            ];

            $html = $twig->render('pinboard-submission.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    public function catalogueRequestClient(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => "CATALOGUE REQUEST - Md Shofiul Alam",
                'catalogue_view' => 'Online/ Mailed to me',
                'company' => '',
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'email' => '',
                'street_address' => '',
                'city' => '',
                'state' => '',
                'postcode' => '',
                'hear_about_us' => '',
            ];

            $html = $twig->render('catalogue-request-admin.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function physicalMailout(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'Thank you for browsing our catalogue online',
                'client_name' => "[Client's name]",
                'catalogue_year' => '2026',
                'showroom_tour_url' => 'http://54.252.147.60:8089/contact-us#th-showrooms',
                'projects_url' => 'http://54.252.147.60:8089/projects',
                'phone' => '02 9557 3055',
                'sales_email' => 'sales@krost.com.au',
            ];

            $html = $twig->render('physical-mailout.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    public function onlineVersion(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'Thank you for browsing our catalogue online',
                'client_name' => '',
                'showroom_tour_url' => 'http://54.252.147.60:8089/contact-us#th-showrooms',
                'projects_url' => 'https://www.krost.com.au/Projects',
                'phone' => '02 9557 3055',
                'sales_email' => 'sales@krost.com.au',
            ];

            $html = $twig->render('online-version.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    // reschedule booking emails
    public function rescheduleBooking(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'salesperson_name' => 'Michael Smith',
                'original_date'    => '15 April 2026, 9:00 AM',
                'reschedule_link'  => 'https://yourdomain.com/reschedule/abc123',
            ];

            $html = $twig->render('reschedule-booking.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    // cancel booking emails for physical tour
    public function cancelBookingPhysicalTour(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'client_name'      => 'John Doe',
                'date'             => '15 April 2026',
                'reschedule_link'  => 'https://yourdomain.com/book-again',
            ];

            $html = $twig->render('cancel-booking-physical-tour.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    

    // internal notification emails for reschedule or cancel booking
    public function cancelBookingVirtualMeeting(Request $request): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);

            // TODO: replace with values from the database
            $context = [
                'subject' => 'Internal Notification - Cancelled Online Meeting Booking',
                'recipient_name' => '',
                'meeting_date' => '',
                'meeting_time' => '',
                'platform_url' => '#',
                'platform_label' => '',
                'client_email' => '',
            ];

            $html = $twig->render('cancel-booking-virtual-meeting.html.twig', $context);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }


    // Reschedule/ Cancel Template Email for Salesperson to send the Client (if Salesperson cancels)
    // Email Subject: Rescheduling your Appointment with Krost 
    // 1. 
    public function rescheduleBookingEmail(): Response
    {
        try {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);
            $context = [
                'client_name'      => 'John Doe',
                'salesperson_name' => 'Michael Smith',
                'original_date'    => date('d F Y', strtotime($booking->date)),
                'reschedule_link'  => base_url('/reschedule/' . $booking->token),
            ];
            $html = $twig->render('reschedule-request.html.twig', $context);
            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    // 2. Automated Cancellation Email from the system (to Client) - Showroom Tour 
// Email Subject:  Booking Cancelled: Showroom Tour with Krost 
// 2. 
public function cancelBookingEmail(): Response
{
    try {
        $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
        $twig = new Environment($loader, [
            'cache' => false,
            'autoescape' => 'html',
        ]);
        $cancelShowroomContext = [
            'client_name'     => 'John Doe',
            'date'            => '15 April 2026',
            'reschedule_link' => 'https://yourdomain.com/reschedule/abc123',
        ];
        $html = $twig->render('booking-cancelled-showroom.html.twig', $cancelShowroomContext);
        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($html);
    } catch (Exception $e) {
        return $this->handleException($e);
    }
}

// Automated Cancellation Email from the system  (to Client) - Online Consultation 
// 3. Email Subject:  Booking Cancelled: Online Consultation with Krost 
public function cancelBookingOnlineConsultation(): Response
{
    try {
        $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
        $twig = new Environment($loader, [
            'cache' => false,
            'autoescape' => 'html',
        ]);
        $onlineCancelContext = [
            'client_name'     => $booking->customer_name,
            'date'            => date('d F Y', strtotime($booking->date)),
            'reschedule_link' => base_url('/reschedule/' . $booking->token),
        ];
        $html = $twig->render('online-consultation-cancelled.html.twig', $cancelOnlineConsultationContext);
        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    // Automated Cancellation Notification email from the system - to Internal Sales 
    // Email Subject:  CANCELLATION: [Client Name] – [Showroom Location]
public function cancelBookingNotification(): Response
{
    try {
        $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/src/emailtemplate');
        $twig = new Environment($loader, [
            'cache' => false,
            'autoescape' => 'html',
        ]);
        $adminCancelContext = [
            'client_name'      => $booking->customer_name,
            'company_name'     => $booking->company_name ?? 'N/A',
            'original_time'    => date('d F Y @ h:i A', strtotime($booking->start_time)),
            'salesperson_name' => $booking->salesperson->name ?? 'Unassigned',
        ];
        $html = $twig->render('booking-cancelled-admin.html.twig', $cancelShowroomContext);
        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($html);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}
