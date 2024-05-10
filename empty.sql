USE [ifrsEY]
GO

/****** Object:  StoredProcedure [dbo].[emptyTable]    Script Date: 11/10/2021 7:28:43 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

Create Proc [dbo].[emptyTable] 
@user nvarchar(70)
as
declare  @query nvarchar(70);
 
 set @query=CONCAT('Comprehensive_Report',@user);
 
if  (exists(Select * from INFORMATION_SCHEMA.TABLES where TABLE_NAME = @query))
begin
	drop table OutPut_Muhammad_Sufyan
	drop table ECLGeneralInput_Muhammad_Sufyan
end


exec('select top 0 * into '+@query+' from Comprehensive_Report')
exec('select top 0 * into ECL_GeneralInput_abc from ECL_GeneralInput')

insert into ECL_GeneralInput_abc select top 1000 * from ECL_GeneralInput


DELETE FROM Comprehensive_Report WHERE by_user=@user;
DELETE FROM ConsolidatedLGDCalculated WHERE by_user=@user;
DELETE FROM CostOutput WHERE by_user=@user;
DELETE FROM ECL_GeneralInput WHERE by_user=@user;
DELETE FROM ForwordFile WHERE by_user=@user;
DELETE FROM ForwordLooking_PD WHERE by_user=@user;
DELETE FROM LGD_CostInput WHERE by_user=@user;
DELETE FROM LGD_General_Input WHERE by_user=@user;
DELETE FROM LGD_Output WHERE by_user=@user;
DELETE FROM LGD_RecoveryInput WHERE by_user=@user;
DELETE FROM OutPut WHERE by_user=@user;
DELETE FROM OutputLGD WHERE by_user=@user;
DELETE FROM RatedPDLogit WHERE by_user=@user;
DELETE FROM RecoveryOutputLGD WHERE by_user=@user;
DELETE FROM RetailsPD WHERE by_user=@user;
GO


