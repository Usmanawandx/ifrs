//------------------------------------------------------------------------------
// <auto-generated>
//     This code was generated from a template.
//
//     Manual changes to this file may cause unexpected behavior in your application.
//     Manual changes to this file will be overwritten if the code is regenerated.
// </auto-generated>
//------------------------------------------------------------------------------

namespace LMS.Models
{
    using System;
    using System.Collections.Generic;
    
    public partial class RetailsPD
    {
        public int id { get; set; }
        public System.DateTime Date { get; set; }
        public string CustomerID { get; set; }
        public string DealID { get; set; }
        public string CustomerName { get; set; }
        public string DealType { get; set; }
        public string DealTypeDesc { get; set; }
        public Nullable<double> AmountOS { get; set; }
        public double DaysPastDue { get; set; }
        public string Segment { get; set; }
        public int DPD_Bucket { get; set; }
        public Nullable<System.DateTime> MISDate { get; set; }
        public string by_user { get; set; }
    }
}
