<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A0A0C">
    <title>Car Rental Manager | Make every rental easier</title>
    <link rel="stylesheet" href="{{ asset('css/car-rental-style.css') }}">
</head>
<body class="cr-site">
    <header class="cr-header">
        <nav class="cr-nav cr-container" aria-label="Main navigation">
            <a class="cr-brand" href="#home" aria-label="Car Rental Manager home">
                <span class="cr-brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 13.5 5.7 8.8A2 2 0 0 1 7.6 7.5h8.8a2 2 0 0 1 1.9 1.3l1.7 4.7v4a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1V17h-9v.5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="M5 13h14M7.5 10.5h9M7 15.5h.01M17 15.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </span>
                <span>Car Rental <strong>Manager</strong></span>
            </a>

            <button class="cr-menu-toggle" type="button" aria-expanded="false" aria-controls="cr-primary-links" aria-label="Open navigation menu">
                <span></span><span></span>
            </button>

            <div class="cr-nav-links" id="cr-primary-links">
                <a href="{{ route('cars.cars_list') }}">Fleet</a>
                <a href="#features">Features</a>
                <a href="#pricing">Pricing</a>
                <a href="#testimonials">Testimonials</a>
            </div>

            <div class="cr-nav-actions">
                <a class="cr-login-link" href="{{ route('login') }}">Log In</a>
                <a class="cr-button cr-button-primary cr-button-small" href="{{ route('booking.create') }}">Book a Car <span aria-hidden="true">↗</span></a>
            </div>
        </nav>
    </header>

    <main>
        <section class="cr-hero cr-dark-section" id="home">
            <div class="cr-hero-glow cr-hero-glow-one" aria-hidden="true"></div>
            <div class="cr-hero-glow cr-hero-glow-two" aria-hidden="true"></div>
            <div class="cr-container cr-hero-content">
                <a class="cr-eyebrow cr-eyebrow-dark" href="#features">
                    <span class="cr-pulse-dot" aria-hidden="true"></span>
                    A clearer way to manage every rental
                    <span aria-hidden="true">↗</span>
                </a>
                <h1>Car rentals, without<br><span>the extra hassle.</span></h1>
                <p class="cr-hero-copy">Keep your fleet, bookings, and customer details moving together. Spend less time switching screens and more time delivering a great rental experience.</p>
                <div class="cr-hero-actions">
                    <a class="cr-button cr-button-primary" href="{{ route('cars.cars_list') }}">Explore the fleet <span aria-hidden="true">↗</span></a>
                    <a class="cr-button cr-button-outline" href="#features">See how it works <span aria-hidden="true">↓</span></a>
                </div>

                <div class="cr-preview" aria-label="Fleet dashboard preview">
                    <div class="cr-preview-topbar">
                        <div class="cr-window-controls" aria-hidden="true"><i></i><i></i><i></i></div>
                        <span>Fleet overview</span>
                        <span class="cr-preview-live"><i aria-hidden="true"></i> Sample data</span>
                    </div>
                    <div class="cr-preview-body">
                        <aside class="cr-preview-sidebar" aria-hidden="true">
                            <span class="cr-sidebar-logo"><i></i><i></i><i></i></span>
                            <span class="cr-sidebar-item cr-sidebar-item-active"></span>
                            <span class="cr-sidebar-item"></span>
                            <span class="cr-sidebar-item"></span>
                            <span class="cr-sidebar-item"></span>
                        </aside>
                        <div class="cr-preview-main">
                            <div class="cr-preview-heading">
                                <div><small>DEMO DATA · ILLUSTRATIVE</small><h2>Your fleet at a glance</h2></div>
                                <span class="cr-preview-filter">All locations <b aria-hidden="true">⌄</b></span>
                            </div>
                            <div class="cr-preview-stats">
                                <article><span>Fleet vehicles</span><strong>128</strong><small class="cr-preview-neutral">Across your locations</small></article>
                                <article><span>On rent today</span><strong>86</strong><small class="cr-preview-positive">↑ Active bookings</small></article>
                                <article><span>Ready to rent</span><strong>32</strong><small class="cr-preview-neutral">Available vehicles</small></article>
                            </div>
                            <div class="cr-preview-lower">
                                <div class="cr-chart-card">
                                    <div class="cr-chart-heading"><span>Booking activity</span><small>Last 7 days <b aria-hidden="true">⌄</b></small></div>
                                    <div class="cr-chart" aria-hidden="true">
                                        <span class="cr-chart-y cr-chart-y-one"></span><span class="cr-chart-y cr-chart-y-two"></span><span class="cr-chart-y cr-chart-y-three"></span>
                                        <svg viewBox="0 0 560 130" preserveAspectRatio="none">
                                            <defs><linearGradient id="cr-chart-fill" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#F97316" stop-opacity=".22"/><stop offset="1" stop-color="#F97316" stop-opacity="0"/></linearGradient></defs>
                                            <path d="M0 105 C38 98 50 76 90 83 S145 93 178 59 S240 65 270 48 S320 79 357 54 S410 58 448 28 S510 48 560 12 V130 H0Z" fill="url(#cr-chart-fill)"/>
                                            <path d="M0 105 C38 98 50 76 90 83 S145 93 178 59 S240 65 270 48 S320 79 357 54 S410 58 448 28 S510 48 560 12" fill="none" stroke="#F97316" stroke-width="3" vector-effect="non-scaling-stroke"/>
                                        </svg>
                                    </div>
                                    <div class="cr-chart-days" aria-hidden="true"><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span></div>
                                </div>
                                <div class="cr-preview-activity">
                                    <div class="cr-chart-heading"><span>Recent bookings</span><a href="{{ route('show_booking_list') }}">View all ↗</a></div>
                                    <div class="cr-booking-row"><i class="cr-booking-avatar cr-avatar-orange">JD</i><span><b>Jordan Davis</b><small>Toyota Camry</small></span><em class="cr-status-ready">Confirmed</em></div>
                                    <div class="cr-booking-row"><i class="cr-booking-avatar cr-avatar-blue">MK</i><span><b>Morgan Kim</b><small>Honda CR-V</small></span><em class="cr-status-progress">In progress</em></div>
                                    <div class="cr-booking-row"><i class="cr-booking-avatar cr-avatar-violet">AR</i><span><b>Alex Rivera</b><small>Ford Escape</small></span><em class="cr-status-ready">Confirmed</em></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="cr-preview-caption"><span aria-hidden="true">✦</span> One organized workspace. A smoother day for your team.</p>
            </div>

            <div class="cr-brand-wall">
                <div class="cr-container">
                    <p class="cr-brand-wall-label">A fleet that fits your business</p>
                    <div class="cr-marquee" aria-label="Popular vehicle makes">
                        <div class="cr-marquee-track">
                            <span>TOYOTA</span><span>HONDA</span><span>FORD</span><span>BMW</span><span>HYUNDAI</span><span>VOLKSWAGEN</span><span>NISSAN</span><span>KIA</span>
                            <span aria-hidden="true">TOYOTA</span><span aria-hidden="true">HONDA</span><span aria-hidden="true">FORD</span><span aria-hidden="true">BMW</span><span aria-hidden="true">HYUNDAI</span><span aria-hidden="true">VOLKSWAGEN</span><span aria-hidden="true">NISSAN</span><span aria-hidden="true">KIA</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cr-feature-showcase cr-light-section" id="features">
            <div class="cr-container">
                <div class="cr-section-intro cr-section-intro-left">
                    <span class="cr-eyebrow cr-eyebrow-light">Everything in sync</span>
                    <h2>Run your rental business<br>from one clear view.</h2>
                    <p>From the first reservation to the return, keep the details organized and your team in the loop.</p>
                </div>
                <div class="cr-showcase-grid">
                    <div class="cr-feature-tabs" role="tablist" aria-label="Workspace features">
                        <button class="cr-feature-tab is-active" id="cr-tab-fleet" type="button" role="tab" aria-selected="true" aria-controls="cr-panel-fleet" data-feature-tab="fleet">
                            <span class="cr-tab-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M3 13.5 4.8 8.8a2 2 0 0 1 1.9-1.3h10.6a2 2 0 0 1 1.9 1.3l1.8 4.7v4a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1V17h-11v.5a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M4 13h16M7 10h10M7 15h.01M17 15h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>
                            <span><strong>Fleet at a glance</strong><small>Keep vehicle details and availability organized.</small></span><span class="cr-tab-arrow" aria-hidden="true">↗</span>
                        </button>
                        <button class="cr-feature-tab" id="cr-tab-bookings" type="button" role="tab" aria-selected="false" aria-controls="cr-panel-bookings" data-feature-tab="bookings">
                            <span class="cr-tab-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="15.5" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M7.5 3.5v3M16.5 3.5v3M3.5 9.5h17M8 13h3M8 16h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>
                            <span><strong>Simple booking flow</strong><small>Keep reservations and rental activity together.</small></span><span class="cr-tab-arrow" aria-hidden="true">↗</span>
                        </button>
                        <button class="cr-feature-tab" id="cr-tab-customers" type="button" role="tab" aria-selected="false" aria-controls="cr-panel-customers" data-feature-tab="customers">
                            <span class="cr-tab-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M2.8 19.5a6.2 6.2 0 0 1 12.4 0M16 5.2a3.5 3.5 0 0 1 0 6.6M17 14a5.6 5.6 0 0 1 4.2 5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>
                            <span><strong>Customer records</strong><small>Find important renter details when you need them.</small></span><span class="cr-tab-arrow" aria-hidden="true">↗</span>
                        </button>
                    </div>

                    <div class="cr-dashboard-stage">
                        <div class="cr-stage-orbit cr-stage-orbit-one" aria-hidden="true"></div>
                        <div class="cr-stage-orbit cr-stage-orbit-two" aria-hidden="true"></div>
                        <article class="cr-dashboard-card cr-dashboard-panel is-visible" id="cr-panel-fleet" role="tabpanel" aria-labelledby="cr-tab-fleet" data-feature-panel="fleet">
                            <div class="cr-dash-card-head"><span class="cr-dash-icon cr-dash-icon-orange" aria-hidden="true">↗</span><span class="cr-dash-more" aria-hidden="true">•••</span></div>
                            <small class="cr-dash-kicker">FLEET INVENTORY</small>
                            <div class="cr-dash-big-number">128 <span>vehicles</span></div>
                            <div class="cr-dash-progress"><span></span></div>
                            <div class="cr-dash-legend"><span><i class="cr-legend-dot cr-legend-orange"></i>On rent <b>86</b></span><span><i class="cr-legend-dot cr-legend-green"></i>Available <b>32</b></span><span><i class="cr-legend-dot cr-legend-gray"></i>In service <b>10</b></span></div>
                            <div class="cr-dash-foot"><span>Updated just now</span><span class="cr-dash-positive">Fleet status <b>↗</b></span></div>
                        </article>
                        <article class="cr-dashboard-card cr-dashboard-panel" id="cr-panel-bookings" role="tabpanel" aria-labelledby="cr-tab-bookings" data-feature-panel="bookings" hidden>
                            <div class="cr-dash-card-head"><span class="cr-dash-icon cr-dash-icon-blue" aria-hidden="true">▦</span><span class="cr-dash-more" aria-hidden="true">•••</span></div>
                            <small class="cr-dash-kicker">BOOKING OVERVIEW</small>
                            <div class="cr-dash-big-number">Today <span>at a glance</span></div>
                            <div class="cr-mini-calendar"><span>MON<strong>14</strong></span><span class="is-current">TUE<strong>15</strong></span><span>WED<strong>16</strong></span><span>THU<strong>17</strong></span><span>FRI<strong>18</strong></span></div>
                            <div class="cr-booking-summary"><span><i class="cr-legend-dot cr-legend-orange"></i>Upcoming reservations</span><strong>24</strong></div>
                            <div class="cr-dash-foot"><span>Bookings in one place</span><span class="cr-dash-positive">View schedule <b>↗</b></span></div>
                        </article>
                        <article class="cr-dashboard-card cr-dashboard-panel" id="cr-panel-customers" role="tabpanel" aria-labelledby="cr-tab-customers" data-feature-panel="customers" hidden>
                            <div class="cr-dash-card-head"><span class="cr-dash-icon cr-dash-icon-violet" aria-hidden="true">◎</span><span class="cr-dash-more" aria-hidden="true">•••</span></div>
                            <small class="cr-dash-kicker">CUSTOMER DIRECTORY</small>
                            <div class="cr-customer-preview"><i class="cr-customer-avatar cr-avatar-orange">JD</i><span><b>Jordan Davis</b><small>jordan@example.com</small></span><em>Active</em></div>
                            <div class="cr-customer-preview"><i class="cr-customer-avatar cr-avatar-blue">MK</i><span><b>Morgan Kim</b><small>morgan@example.com</small></span><em>Active</em></div>
                            <div class="cr-customer-preview"><i class="cr-customer-avatar cr-avatar-violet">AR</i><span><b>Alex Rivera</b><small>alex@example.com</small></span><em>New</em></div>
                            <div class="cr-dash-foot"><span>Customer details, organized</span><span class="cr-dash-positive">Open directory <b>↗</b></span></div>
                        </article>
                        <div class="cr-floating-note cr-floating-note-top"><span class="cr-note-check" aria-hidden="true">✓</span> All the details, together</div>
                        <div class="cr-floating-note cr-floating-note-bottom"><span aria-hidden="true">✦</span> Less admin. More momentum.</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cr-integrations cr-dark-section" id="integrations">
            <div class="cr-integration-grid" aria-hidden="true"></div>
            <div class="cr-container cr-integration-content">
                <span class="cr-eyebrow cr-eyebrow-dark">Connected workflow</span>
                <h2>The tools your rental day<br><span>already depends on.</span></h2>
                <p>Keep the essentials close at hand with a workspace built around your fleet, bookings, and people.</p>
                <div class="cr-integration-orbit" aria-label="Workspace tools illustration">
                    <div class="cr-orbit-ring cr-orbit-ring-one" aria-hidden="true"></div>
                    <div class="cr-orbit-ring cr-orbit-ring-two" aria-hidden="true"></div>
                    <span class="cr-orbit-node cr-node-one"><i aria-hidden="true">▣</i><b>Payments</b></span>
                    <span class="cr-orbit-node cr-node-two"><i aria-hidden="true">⌖</i><b>Locations</b></span>
                    <span class="cr-orbit-node cr-node-three"><i aria-hidden="true">▦</i><b>Calendar</b></span>
                    <span class="cr-orbit-node cr-node-four"><i aria-hidden="true">✉</i><b>Updates</b></span>
                    <span class="cr-orbit-node cr-node-five"><i aria-hidden="true">▤</i><b>Reports</b></span>
                    <span class="cr-orbit-node cr-node-six"><i aria-hidden="true">◎</i><b>Customers</b></span>
                    <div class="cr-orbit-center"><span class="cr-brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 13.5 5.7 8.8A2 2 0 0 1 7.6 7.5h8.8a2 2 0 0 1 1.9 1.3l1.7 4.7v4a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1V17h-9v.5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M5 13h14M7.5 10.5h9M7 15.5h.01M17 15.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span></div>
                </div>
                <p class="cr-integration-note"><span aria-hidden="true">✦</span> Your operation, connected from one central place.</p>
            </div>
        </section>

        <section class="cr-feature-matrix cr-light-section">
            <div class="cr-container">
                <div class="cr-section-intro">
                    <span class="cr-eyebrow cr-eyebrow-light">Made for the daily details</span>
                    <h2>All the moving parts.<br><span>One smoother workflow.</span></h2>
                    <p>The essentials for running a car rental operation, arranged to be simple to find and easy to use.</p>
                </div>
                <div class="cr-feature-grid">
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-orange" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M3 13.5 4.8 8.8a2 2 0 0 1 1.9-1.3h10.6a2 2 0 0 1 1.9 1.3l1.8 4.7v4a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1V17h-11v.5a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M4 13h16M7 10h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3>Fleet inventory</h3><p>Keep vehicle information organized and get a clearer view of your fleet.</p><a href="{{ route('cars.cars_list') }}" aria-label="Explore the fleet">Explore fleet <span aria-hidden="true">↗</span></a></article>
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-blue" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="15.5" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M7.5 3.5v3M16.5 3.5v3M3.5 9.5h17M8 13h3M8 16h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3>Booking management</h3><p>Keep rental dates, reservations, and customer details connected.</p><a href="{{ route('show_booking_list') }}" aria-label="View bookings">View bookings <span aria-hidden="true">↗</span></a></article>
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-violet" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M2.8 19.5a6.2 6.2 0 0 1 12.4 0M16 5.2a3.5 3.5 0 0 1 0 6.6M17 14a5.6 5.6 0 0 1 4.2 5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3>Customer records</h3><p>Access useful renter information and booking history in one place.</p><a href="{{ route('register') }}" aria-label="Get started">Get started <span aria-hidden="true">↗</span></a></article>
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-green" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5h16v15H4zM8 3.5v4M16 3.5v4M4 10h16M8 14h2M14 14h2M8 17h2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3>Rental schedule</h3><p>Get a straightforward view of upcoming and active reservations.</p><a href="{{ route('show_booking_list') }}" aria-label="See the schedule">See the schedule <span aria-hidden="true">↗</span></a></article>
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-pink" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 6.5h16M4 12h16M4 17.5h10M7 4v5M17 9.5v5M10 15v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3>Easy fleet search</h3><p>Find vehicles by their details with a simpler browsing experience.</p><a href="{{ route('cars.cars_list') }}" aria-label="Browse vehicles">Browse vehicles <span aria-hidden="true">↗</span></a></article>
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-yellow" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 19V9M10 19V5M16 19v-7M22 19V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M2.5 19.5h20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span><h3>Operations overview</h3><p>Bring day-to-day information into a focused workspace for your team.</p><a href="{{ route('login') }}" aria-label="Sign in to your dashboard">Sign in <span aria-hidden="true">↗</span></a></article>
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-teal" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3.5 20 7v5.2c0 4.2-3.1 7-8 8.8-4.9-1.8-8-4.6-8-8.8V7l8-3.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m8.5 12 2.2 2.2 4.8-4.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3>Role-based access</h3><p>Keep team workflows clear with access tailored to the right people.</p><a href="{{ route('login') }}" aria-label="Access your workspace">Access workspace <span aria-hidden="true">↗</span></a></article>
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-indigo" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5h16v13H4zM8 9h8M8 12h8M8 15h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3>Rental details</h3><p>Keep key reservation information easy to review as plans change.</p><a href="{{ route('show_booking_list') }}" aria-label="Review reservations">Review reservations <span aria-hidden="true">↗</span></a></article>
                    <article class="cr-feature-card"><span class="cr-feature-card-icon cr-icon-red" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4.5 4.5M8 10.8h5.6M10.8 8v5.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3>Rental-ready browsing</h3><p>Help customers explore available vehicles and find their next ride.</p><a href="{{ route('cars.cars_list') }}" aria-label="Find a car">Find a car <span aria-hidden="true">↗</span></a></article>
                </div>
            </div>
        </section>

        <section class="cr-metrics cr-dark-section" id="pricing">
            <div class="cr-container">
                <div class="cr-metrics-heading">
                    <span class="cr-eyebrow cr-eyebrow-dark">Built to move with you</span>
                    <h2>A better rental day<br><span>starts with a clearer view.</span></h2>
                    <p>Replace these marked sample values with your verified business metrics before publishing.</p>
                </div>
                <div class="cr-metrics-grid">
                    <article class="cr-metric-card"><span class="cr-placeholder-tag">Placeholder</span><strong>XX+</strong><span>Locations served</span><small>Replace with verified data</small></article>
                    <article class="cr-metric-card"><span class="cr-placeholder-tag">Placeholder</span><strong>XXX+</strong><span>Vehicles in managed fleets</span><small>Replace with verified data</small></article>
                    <article class="cr-metric-card"><span class="cr-placeholder-tag">Placeholder</span><strong>XXK+</strong><span>Bookings managed</span><small>Replace with verified data</small></article>
                    <article class="cr-metric-card"><span class="cr-placeholder-tag">Placeholder</span><strong>$XXM+</strong><span>Transaction volume</span><small>Replace with verified data</small></article>
                </div>
            </div>
        </section>

        <section class="cr-testimonials cr-light-section" id="testimonials">
            <div class="cr-container">
                <div class="cr-section-intro">
                    <span class="cr-eyebrow cr-eyebrow-light">The experience matters</span>
                    <h2>Thoughtful tools for<br><span>people on the move.</span></h2>
                    <p>Sample testimonial cards are shown below for layout only. Replace them with approved customer reviews.</p>
                </div>
                <div class="cr-testimonial-grid">
                    <article class="cr-review-card">
                        <div class="cr-review-top"><span class="cr-review-placeholder">Placeholder review</span><span class="cr-stars" aria-label="5 out of 5 stars">★★★★★</span></div>
                        <blockquote>“Replace this sample with a verified customer quote about managing fleet availability.”</blockquote>
                        <div class="cr-reviewer"><span class="cr-review-avatar cr-review-avatar-one" aria-hidden="true">JD</span><span><strong>Customer name</strong><small>Fleet operator · Placeholder</small></span><span class="cr-quote-mark" aria-hidden="true">“</span></div>
                    </article>
                    <article class="cr-review-card cr-review-card-featured">
                        <div class="cr-review-top"><span class="cr-review-placeholder">Placeholder review</span><span class="cr-stars" aria-label="5 out of 5 stars">★★★★★</span></div>
                        <blockquote>“Add an approved review here that reflects a real experience with your rental workflow.”</blockquote>
                        <div class="cr-reviewer"><span class="cr-review-avatar cr-review-avatar-two" aria-hidden="true">MK</span><span><strong>Customer name</strong><small>Rental manager · Placeholder</small></span><span class="cr-quote-mark" aria-hidden="true">“</span></div>
                    </article>
                    <article class="cr-review-card">
                        <div class="cr-review-top"><span class="cr-review-placeholder">Placeholder review</span><span class="cr-stars" aria-label="5 out of 5 stars">★★★★★</span></div>
                        <blockquote>“Use a real customer quote here to share how your team makes renting simpler.”</blockquote>
                        <div class="cr-reviewer"><span class="cr-review-avatar cr-review-avatar-three" aria-hidden="true">AR</span><span><strong>Customer name</strong><small>Rental customer · Placeholder</small></span><span class="cr-quote-mark" aria-hidden="true">“</span></div>
                    </article>
                </div>
            </div>
        </section>

    </main>

    <footer class="cr-footer cr-dark-section">
        <div class="cr-container">
            <div class="cr-footer-main">
                <div class="cr-footer-brand">
                    <a class="cr-brand" href="#home">
                        <span class="cr-brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 13.5 5.7 8.8A2 2 0 0 1 7.6 7.5h8.8a2 2 0 0 1 1.9 1.3l1.7 4.7v4a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1V17h-9v.5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M5 13h14M7.5 10.5h9M7 15.5h.01M17 15.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                        <span>Car Rental <strong>Manager</strong></span>
                    </a>
                    <p>Making the everyday work of car rentals feel more organized, connected, and straightforward.</p>
                    <a class="cr-footer-email" href="{{ route('register') }}">Create an account <span aria-hidden="true">↗</span></a>
                </div>
                <div class="cr-footer-column"><h3>Services</h3><a href="#features">Fleet management</a><a href="{{ route('show_booking_list') }}">Booking management</a><a href="#features">Customer records</a><a href="#integrations">Workspace tools</a></div>
                <div class="cr-footer-column"><h3>Rentals</h3><a href="{{ route('cars.cars_list') }}">Browse cars</a><a href="{{ route('cars.cars_list') }}">Available vehicles</a><a href="{{ route('login') }}">Manage bookings</a><a href="#features">How it works</a></div>
                <div class="cr-footer-column"><h3>Company</h3><a href="#home">About the platform</a><a href="#testimonials">Testimonials</a><a href="#pricing">Our impact</a><a href="{{ route('register') }}">Get started</a></div>
            </div>
            <div class="cr-footer-bottom"><span>© {{ now()->year }} Car Rental Manager. All rights reserved.</span><span>Designed for smoother journeys. <i aria-hidden="true">✦</i></span><div><a href="#home">Privacy</a><a href="#home">Terms</a></div></div>
        </div>
    </footer>

    <script>
        (() => {
            const menuToggle = document.querySelector('.cr-menu-toggle');
            const navLinks = document.querySelector('#cr-primary-links');

            if (menuToggle && navLinks) {
                menuToggle.addEventListener('click', () => {
                    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
                    menuToggle.setAttribute('aria-expanded', String(!isOpen));
                    menuToggle.setAttribute('aria-label', isOpen ? 'Open navigation menu' : 'Close navigation menu');
                    navLinks.classList.toggle('is-open', !isOpen);
                });

                navLinks.querySelectorAll('a').forEach((link) => {
                    link.addEventListener('click', () => {
                        menuToggle.setAttribute('aria-expanded', 'false');
                        menuToggle.setAttribute('aria-label', 'Open navigation menu');
                        navLinks.classList.remove('is-open');
                    });
                });
            }

            const featureTabs = document.querySelectorAll('[data-feature-tab]');
            const featurePanels = document.querySelectorAll('[data-feature-panel]');

            featureTabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const selectedFeature = tab.dataset.featureTab;
                    featureTabs.forEach((item) => {
                        const selected = item === tab;
                        item.classList.toggle('is-active', selected);
                        item.setAttribute('aria-selected', String(selected));
                    });
                    featurePanels.forEach((panel) => {
                        const selected = panel.dataset.featurePanel === selectedFeature;
                        panel.hidden = !selected;
                        panel.classList.toggle('is-visible', selected);
                    });
                });
            });
        })();
    </script>
</body>
</html>
