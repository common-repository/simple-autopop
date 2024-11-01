<?php

class Simple_AutoPOP_EventGenerator
{
    /**
     * @var bool
     */
    private $activateLinks;

    /**
     * Simple_AutoPOP_EventGenerator constructor.
     * @param $activateLinks
     */
    public function __construct($activateLinks)
    {
        $this->activateLinks = $activateLinks;
    }

    /**
     * @param array $event
     * @return string
     */
    private function createEventHtml($event)
    {
        // srcset="https://www.kiamiller.com/wp-content/uploads/2017/10/Sat-Nam-FEst-480x240.png 480w, https://www.kiamiller.com/wp-content/uploads/2017/10/Sat-Nam-FEst-640x320.png 640w, https://www.kiamiller.com/wp-content/uploads/2017/10/Sat-Nam-FEst-240x120.png 240w"
        //                                                                                         sizes="(max-width: 480px) 100vw, 480px"

        $dateStart = new \DateTime($event['start']);
        $dateStartStr = $dateStart->format('F d');
        $dateEnd = new \DateTime($event['end']);
        $dateEndStr = $dateEnd->format('F d');
        $imageUrl = $event['image_url'] ? $event['image_url'] : $event['event_logo'];
        $eventId = $event['event_id'];
        $orgName = reset($event['host_orgs']);
        $locationItems = [];

        if ($event['location_name']) {
            $locationItems[] = '<span class="simple-autopop-locality">'.$event['location_name'].'</span>';
        }

        if ($event['location_street']) {
            $locationItems[] = '<span class="simple-autopop-street">'.$event['location_street'].'</span>';
        }

        if ($event['location_city']) {
            $locationItems[] = '<span class="simple-autopop-region">'.$event['location_city'].'</span>';
        }

        if ($event['location_province']) {
            $locationItems[] = '<span class="row-country-name">'.$event['location_province'].'</span>';
        }

        return '
        <div class="type-row_events row-clearfix" id="simple-autopop-event-'.$eventId.'">
                        <div class="simple-autopop-calendar-event">
                            <div class="list-info">
                                <div class="simple-autopop-event-image">
                                    <a href="' . $event['original_url'] . '" target="_blank">
                                        <img width="480"
                                            height="240"
                                            src="' . $imageUrl . '"
                                            class="attachment-event-medium size-event-medium"
                                            alt="">
                                    </a>
                                </div>  
                                <h2 class="simple-autopop-title">
                                    <a href="' . $event['original_url'] . '" rel="bookmark" target="_blank">' . $event['title'] . '</a>
                                </h2>
                                <div class="simple-autopop-orgname">' . $orgName . '</div>
                                <div class="simple-autopop-duration">
                                <span class="row-event-date-start">' . $dateStartStr . '</span>' .
($dateStartStr !== $dateEndStr ? '- <span class="row-event-date-end">' . $dateEndStr . '</span>' : '') . '
                                </div>
                                <div class="simple-autopop-location">' . implode(', ', $locationItems) . '</div>
                            </div>
                        </div>
                    </div>
        ';
    }

    /**
     * @param array $events
     * @param bool $isWidget
     * @return string
     */
    public function generateFrom($events, $isWidget = false)
    {
        $eventContent = '';
        foreach ($events as $event) {
            $eventContent .= $this->createEventHtml($event);
        }
        $title = get_option(Simple_AutoPOP::PREFIX . '_title', '');

        $html = '
<div class="home-featured-events ' . ($isWidget ? 'simple-autopop-widget-area' : '') . '">
    <div class="wrap">
        <section id="simple-autopop-adv-list-widget-7" class="widget simple-autopop-adv-list-widget">
            <div class="widget-wrap">
                <h4 class="widget-title widgettitle">' . $title . '</h4>
                <div class="event-widget-container">' . $eventContent . '</div>
                <img src="http://www.eventupon.com/img/powered-by-eventupon.png" width="155" height="18" alt="Powered by EventUpon" style="margin: 10px 0 0 10px;box-shadow: none;">
            </div>
        </section>

    </div>
</div>';
/*
        <script type="application/ld+json">
            [{"@context":"http://schema.org","@type":"Event","name":"Sat Nam Fest","description":"&lt;p&gt;Sat Nam Fest Joshua Tree\u00a0is an opportunity to immerse yourself in the joy, transformation, and rejuvenation of Kundalini Yoga, sacred chant, and creativity as you re-discover and return to your true self.&lt;/p&gt;\\n","image":"https://www.kiamiller.com/wp-content/uploads/2017/10/Sat-Nam-FEst.png","url":"https://www.kiamiller.com/event/sat-nam-fest-2/","startDate":"2018-04-11T00:00:00-07:00","endDate":"2018-04-15T23:59:59-07:00","location":{"@type":"Place","name":"Joshua Tree Retreat Center","description":"&lt;p&gt;Our Southern California Retreat Center can sleep from 10 to 280 participants and seats up to 500 in our largest indoor meeting space. Most of the unique buildings at our site were built by world-renowned architects, Frank Lloyd Wright and his son, Lloyd. Joshua Tree Retreat Center is unlike the more commercially oriented Hotels. We [&hellip;]&lt;/p&gt;\\n","image":"https://www.kiamiller.com/wp-content/uploads/2015/02/joshua-tree-sanctuary.jpg","url":"https://www.kiamiller.com/venue/joshua-tree-retreat-center/","address":{"@type":"PostalAddress","streetAddress":"59700 29 Palms Hwy","addressLocality":"Joshua Tree","addressRegion":"CA","postalCode":"92252","addressCountry":"United States"},"geo":{"@type":"GeoCoordinates","latitude":34.1348976,"longitude":-116.3608229},"telephone":"(760) 365-837","sameAs":"http://joshuatreeretreatcenter.squarespace.com/"},"organizer":{"@type":"Person","name":"Kia Miller","description":"&lt;p&gt;Kia Miller is a devoted Yogini and teacher who imparts her wonderful passion for life and well-being in her teaching. She is certified in the Ashtanga / Vinyasa Flow tradition, as well as Kundalini Yoga as taught by Yogi Bhajan. Her style pulls from multiple yogic disciplines, and is both intuitive and steeped in the [&hellip;]&lt;/p&gt;\\n","image":"https://www.kiamiller.com/wp-content/uploads/2015/01/kia-gravatar.jpg","url":"https://www.kiamiller.com/organizer/kia-miller/","telephone":"","email":"","sameAs":"http://kiamiller.com"}},{"@context":"http://schema.org","@type":"Event","name":"DOUBLE MODULE: Prana &#038; Pranayama with Meditation &#038; The Mind","description":"&lt;p&gt;Double module includes two 60-hour certification courses: Prana and Pranayama and Meditation and the Mind.&lt;/p&gt;\\n","image":"https://www.kiamiller.com/wp-content/uploads/2015/09/Kia-practicing-yoga-at-home-cropped.jpg","url":"https://www.kiamiller.com/event/prana-pranayama-meditation-mind/","startDate":"2018-04-16T00:00:00-07:00","endDate":"2018-04-27T23:59:59-07:00","location":{"@type":"Place","name":"Mount Madonna Center","description":"","url":"https://www.kiamiller.com/venue/mount-madonna-center/","address":{"@type":"PostalAddress","streetAddress":"445 Summit Rd","addressLocality":"Watsonville","addressRegion":"CA","postalCode":"95076","addressCountry":"United States"},"geo":{"@type":"GeoCoordinates","latitude":37.027052,"longitude":-121.7390955},"telephone":"(408) 847-0406","sameAs":"https://www.mountmadonna.org/"},"offers":{"@type":"Offer","price":"1700","url":"https://www.kiamiller.com/event/prana-pranayama-meditation-mind/"}},{"@context":"http://schema.org","@type":"Event","name":"Prana &#038; Pranayama: Unveiling the Sacred Science of the Breath","description":"&lt;p&gt;This 60-hour certification course offers an in-depth look at prana and pranayama. Participants will explore the experiences delivered through certain breathing techniques and learn how to integrate them into their own practice and life and how to safely share these practices with their students.&lt;/p&gt;\\n","image":"https://www.kiamiller.com/wp-content/uploads/2015/02/Kia-prana-pranayama-1200x600.jpg","url":"https://www.kiamiller.com/event/prana-pranayama-unveiling-sacred-science-breath-2/","startDate":"2018-04-16T00:00:00-07:00","endDate":"2018-04-21T23:59:59-07:00","location":{"@type":"Place","name":"Mount Madonna Center","description":"","url":"https://www.kiamiller.com/venue/mount-madonna-center/","address":{"@type":"PostalAddress","streetAddress":"445 Summit Rd","addressLocality":"Watsonville","addressRegion":"CA","postalCode":"95076","addressCountry":"United States"},"geo":{"@type":"GeoCoordinates","latitude":37.027052,"longitude":-121.7390955},"telephone":"(408) 847-0406","sameAs":"https://www.mountmadonna.org/"},"offers":{"@type":"Offer","price":"900","url":"https://www.kiamiller.com/event/prana-pranayama-unveiling-sacred-science-breath-2/"}},{"@context":"http://schema.org","@type":"Event","name":"Meditation and The Mind: Awakening Our Spiritual Heart","description":"&lt;p&gt;This 60-hour certification offers an in-depth look at the yogic model of the mind and the practice and application of meditation. Participants will explore the experience delivered through certain meditation techniques and learn how to share these practices with their students.&lt;/p&gt;\\n","image":"https://www.kiamiller.com/wp-content/uploads/2017/08/meditation-hand-e1502132381859.jpg","url":"https://www.kiamiller.com/event/meditation-mind-awakening-spiritual-heart-2/","startDate":"2018-04-22T00:00:00-07:00","endDate":"2018-04-27T23:59:59-07:00","location":{"@type":"Place","name":"Mount Madonna Center","description":"","url":"https://www.kiamiller.com/venue/mount-madonna-center/","address":{"@type":"PostalAddress","streetAddress":"445 Summit Rd","addressLocality":"Watsonville","addressRegion":"CA","postalCode":"95076","addressCountry":"United States"},"geo":{"@type":"GeoCoordinates","latitude":37.027052,"longitude":-121.7390955},"telephone":"(408) 847-0406","sameAs":"https://www.mountmadonna.org/"},"offers":{"@type":"Offer","price":"900","url":"https://www.kiamiller.com/event/meditation-mind-awakening-spiritual-heart-2/"}}]
        </script>
 */
        return $html;
    }
}