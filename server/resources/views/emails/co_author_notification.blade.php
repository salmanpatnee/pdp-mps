@component('mail::message')
# Manuscript Submission Acknowledgment

**Dear {{ $coAuthor->name }},**

We are pleased to inform you that you have been listed as a **co-author** on a newly submitted manuscript.

**Manuscript Reference:** {{ $manuscript->reference_no }}  

**Title:** {{ $manuscript->title }}  
**Submitted By:** {{ $manuscript->author->name }}  
**Journal:** {{ $manuscript->journal->name }}  
**Submission Date:** {{ $manuscript->created_at->format('F j, Y') }}

This email serves as confirmation of your authorship on this submission.  

No further action is required from your side at this stage.

Thank you for your contribution and continued collaboration.

Best regards,  
**{{ config('app.name') }} Editorial Team**
@endcomponent
