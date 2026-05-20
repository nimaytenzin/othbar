<?php

namespace App\Enums;

enum DocumentType: string
{
    case Invoice = 'invoice';
    case CustomerPayment = 'customer_payment';
    case Bill = 'bill';
    case SupplierPayment = 'supplier_payment';
    case Quotation = 'quotation';
    case Contract = 'contract';
    case CreditNote = 'credit_note';
    case DebitNote = 'debit_note';
}
