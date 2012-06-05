<?php

namespace CCETC\NotificationBundle\Delivery;

/**
 * Methods for delivering notifications
 */
class DeliveryHelper {
    
    protected $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * Send all notification instance emails that need to be sent to users that recieve emails at a specific $frequency
     * 
     * @param type $frequency - instantly|periodically|daily|never
     * @return int 
     */
    public function processAndSendNotificationEmails($frequency)
    {
        $userRepository = $this->container->get('doctrine')->getRepository('ApplicationSonataUserBundle:User');
        $notificationInstanceAdmin = $this->container->get('ccetc.notification.admin.notificationinstance');
        $emailsSent = 0;
        
        $users = $userRepository->findBy(array('notificationEmailFrequency' => $frequency));
        
        foreach($users as $user)
        {
            $instancesToEmail = $this->findInstancesByUser($user, true);

            if($instancesToEmail) {
                $this->sendNotificationDigestEmail($user, $instancesToEmail);
                $emailsSent++;
            
                foreach($instancesToEmail as $instance)
                {
                    $instance->setHasBeenEmailed(true);
                    $notificationInstanceAdmin->update($instance);
                }            
            }
        }
        
        return $emailsSent;
    }
    
    /**
     * Send an email to $user listing $instances.
     * 
     * @param type $instance 
     */
    public function sendNotificationDigestEmail($user, $instances)
    {
        $mailer = $this->container->get('mailer');
        
        $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
        $applicationTitle = $this->container->getParameter('fos_user.settings.application_title');
        
        if(count($instances) == 1) $noun = "notification";
        else $noun = "notifications";
        
        $body = '<html>';
        
        $body .= 'You have '.count($instances).' new '.$noun.':<br/><br/> ';
        
        foreach($instances as $instance)
        {
            $notification = $instance->getNotification();
            
            $body .= '<b>'.$notification->getShortMessage().'</b><br/>';
            if($notification->getLongMessage()) $body .= $notification->getLongMessage().'<br/>';
            $body .= '<br/><br/>';
        }
        
        $message = \Swift_Message::newInstance()
                ->setSubject($applicationTitle.' - '.count($instances).' '.ucfirst($noun))
                ->setFrom($fromEmail)
                ->setTo($user->getEmail())
                ->setContentType('text/html')
                ->setBody($body)
        ;
        $mailer->send($message);
    }
    
    
    /**
     * Fine all instances belonging to User.
     * 
     * @param type $user
     * @param bool $active only include inactive or active instances
     * @return type 
     */
    public function findInstancesByUser($user, $active = null)
    {
        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getEntityManager();
        
        if(isset($active)) {
            if($active) $active = 1;
            else $active = 0;
        }
        
        // get all of user's instances
        $query = "
            SELECT   ni, u, n
            FROM     CCETCNotificationBundle:NotificationInstance ni
            JOIN     ni.user u
            JOIN     ni.notification n
            WHERE u.id = '".$user->getId()."'";
        
        if(isset($active)) {
            $query .= "AND ni.active=".$active;
        }

        $query .= " ORDER BY n.datetimeCreated DESC";
        
        return $entityManager->createQuery($query)->getResult();
    }

    
}