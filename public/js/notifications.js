/*
    This code contains notfications management with Pusher.
    Author : EL HAJJAMI JAWAD
    Date : 28/06/2024
    Version : 1.0.0
*/

document.addEventListener('DOMContentLoaded', function () {
    // Initialize Pusher with your Pusher app key
    var pusher = new Pusher('39884f5a414a79159783', {
        cluster: 'mt1',
        encrypted: true
    });

    // Subscribe to the new notes-notifications channel
    var notesChannel = pusher.subscribe('notes-notifications');

    // Bind to the NoteCreated event on the notes-notifications channel
    notesChannel.bind('NoteNotificationEvent', function(data) {
        Livewire.dispatch('noteNotificationRecieved', { notification: data });

        // Play notification sound if needed
        Livewire.on('playSound', (event) => {
            console.log('Play sound');
            const notificationSound = document.getElementById('notification-sound');
            if (notificationSound) {
                notificationSound.play().catch(function (error) {
                    console.error('Error playing sound:', error);
                });
            }
        });
    });

    // Existing code for handling DocumentCreated notifications
    var channel = pusher.subscribe('notifications');
    channel.bind('NotificationEvent', function(data) {
        Livewire.dispatch('notificationReceived', { value: data });

        // Play notification sound
        Livewire.on('playSound', (event) => {
            console.log('Play sound');
            const notificationSound = document.getElementById('notification-sound');
            if (notificationSound) {
                notificationSound.play().catch(function (error) {
                    console.error('Error playing sound:', error);
                });
            }
        });
    });

    // Listen to (marked-as-read-notifications) channel
    let markedAsReadChannel = pusher.subscribe('marked-as-read-notifications');
    markedAsReadChannel.bind('DocumentMarkedAsReadEvent', function(data) {
        Livewire.dispatch('DocumentReadEvent', { notification: data });

        // Play notification sound
        Livewire.on('playSound', (event) => {
            console.log('Play sound');
            const notificationSound = document.getElementById('notification-sound');
            if (notificationSound) {
                notificationSound.play().catch(function (error) {
                    console.error('Error playing sound:', error);
                });
            }
        });
    });
    

    // Existing code for handling dropdownTrigger and notificationList
    const dropdownTrigger = document.querySelector('.notification-indicator');
    const notificationList = document.getElementById('notification-list');
    if (dropdownTrigger && notificationList) {
        dropdownTrigger.addEventListener('click', function () {
            Livewire.dispatch('markAsRead');
        });
    }
});