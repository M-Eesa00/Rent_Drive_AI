import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import listPlugin from "@fullcalendar/list";

const calendarElement = document.getElementById("booking-calendar");

if (calendarElement) {
    const calendar = new Calendar(calendarElement, {
        plugins: [dayGridPlugin, listPlugin],
        initialView: "dayGridMonth",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,listMonth",
        },
        events: {
            url: calendarElement.dataset.eventsUrl,
            failure(error) {
                window.alert(
                    `Could not load calendar bookings: ${error.message}`,
                );
            },
        },
        eventClick(info) {
            const booking = info.event.extendedProps;

            window.alert(
                `${info.event.title}\n` +
                    `Plate: ${booking.plate || "Unknown"}\n` +
                    `Customer: ${booking.customer}\n` +
                    `Status: ${booking.status}`,
            );
        },
    });

    calendar.render();
}

const availabilityForm = document.getElementById("availability-form");

if (availabilityForm) {
    availabilityForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        const startDate = document.getElementById("availability-start").value;
        const endDate = document.getElementById("availability-end").value;
        const message = document.getElementById("availability-message");
        const carList = document.getElementById("available-cars");

        message.textContent = "Checking availability…";
        carList.replaceChildren();

        try {
            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
            });

            const response = await fetch(
                `/admin/calendar/availability?${params.toString()}`,
                { headers: { Accept: "application/json" } },
            );

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || "Availability check failed.");
            }

            if (result.cars.length === 0) {
                message.textContent = "No cars are free for those dates.";
                return;
            }

            message.textContent = `Cars free for ${startDate} to ${endDate}:`;

            for (const car of result.cars) {
                const item = document.createElement("li");
                item.textContent = `${car.name} — ${car.plate_number}`;
                carList.appendChild(item);
            }
        } catch (error) {
            message.textContent = `Could not check availability: ${error.message}`;
        }
    });
}
