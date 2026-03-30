<?php

namespace Database\Seeders;

use App\Models\{Project, Ticket, User};
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $user    = User::where('email', 'demo@servicehub.test')->first();
        $tickets = [
            [
                'project'     => 'Website Redesign',
                'title'       => 'Login page broken after deploy',
                'description' => 'Users are unable to log in since the last deployment on Friday. The error page shows a 500 status with no additional details in production logs.',
                'notes'       => 'Investigated: missing APP_KEY in production .env. Resolved by regenerating the key and restarting php-fpm.',
            ],
            [
                'project'     => 'Website Redesign',
                'title'       => 'Homepage loads slowly on mobile',
                'description' => 'Page load time on mobile devices is around 8–10 seconds. Desktop is fine. Possibly related to unoptimised images or missing lazy loading.',
                'notes'       => null,
            ],
            [
                'project'     => 'ERP Integration',
                'title'       => 'Invoice sync fails for orders above R$10.000',
                'description' => 'The ERP sync job throws a validation exception when the order total exceeds R$10.000. Smaller orders sync correctly.',
                'notes'       => 'Identified a currency parsing bug: the formatter was stripping the decimal separator for values with 5+ digits. Fix deployed to staging.',
            ],
            [
                'project'     => 'Mobile App v2',
                'title'       => 'Push notifications not delivered on iOS 17',
                'description' => 'After the iOS 17 update, push notifications stopped arriving for a subset of users. Android is unaffected.',
                'notes'       => null,
            ],
            [
                'project'     => 'Infrastructure Migration',
                'title'       => 'Database backup job timed out',
                'description' => 'The nightly backup cron job exceeded the 30-minute timeout. The database has grown significantly and the current strategy needs revisiting.',
                'notes'       => 'Switched to incremental backups using WAL archiving. New job completes in under 4 minutes.',
            ],
            [
                'project'     => 'Customer Portal',
                'title'       => 'Users cannot reset their password',
                'description' => 'The password reset email is not being received. SMTP logs show the message is queued but never delivered.',
                'notes'       => null,
            ],
        ];

        foreach ($tickets as $data) {
            $project = Project::where('name', $data['project'])->first();

            $ticket = Ticket::create([
                'project_id'  => $project->id,
                'user_id'     => $user->id,
                'title'       => $data['title'],
                'description' => $data['description'],
            ]);

            if ($data['notes']) {
                $ticket->ticketDetail->update(['notes' => $data['notes']]);
            }
        }
    }
}
