<?php

namespace App\UseCases\JournalEntry;

use App\Models\JournalEntry;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use Illuminate\Support\Collection;


class StoreJournalEntryInteractor extends Collection
{

    public function execute(JournalEntryRequest $journalEntryRequest){
        $journalEntry = JournalEntry::create($journalEntryRequest->toArray());
        return $journalEntry->toArray();
    }

}
